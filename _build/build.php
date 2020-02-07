<?php

class AppTemplatePackage
{
    /** @var modX $modx */
    public $modx;
    /** @var array $config */
    public $config = [];
    /** @var modPackageBuilder $builder */
    public $builder;
    protected $_idx = 1;

    const name = 'AppTemplate';
    const name_lower = 'apptemplate';
    const version = '1.0.0';
    const release = 'pl';


    /**
     * AppTemplatePackage constructor.
     *
     * @param $core_path
     * @param array $config
     */
    public function __construct($core_path, array $config = [])
    {
        /** @noinspection PhpIncludeInspection */
        require $core_path . 'model/modx/modx.class.php';
        /** @var modX $modx */
        $this->modx = new modX();
        $this->modx->initialize('mgr');
        $this->modx->getService('error', 'error.modError');

        $root = dirname(dirname(__FILE__)) . '/';
        $assets = $root . 'assets/components/' . $this::name_lower . '/';
        $core = $root . 'core/components/' . $this::name_lower . '/';

        $this->config = array_merge([
            'log_level' => modX::LOG_LEVEL_INFO,
            'log_target' => 'ECHO',

            'root' => $root,
            'build' => $root . '_build/',
            'elements' => $root . '_build/elements/',
            'resolvers' => $root . '_build/resolvers/',

            'assets' => $assets,
            'core' => $core,
        ], $config);
        $this->modx->setLogLevel($this->config['log_level']);
        $this->modx->setLogTarget($this->config['log_target']);
        if (!XPDO_CLI_MODE) {
            echo '<pre>';
        }

        $this->initialize();
    }


    /**
     * Initialize package builder
     */
    protected function initialize()
    {
        $this->builder = $this->modx->getService('transport.modPackageBuilder');
        $this->builder->createPackage($this::name_lower, $this::version, $this::release);
        $this->builder->registerNamespace($this::name_lower, false, true, '{core_path}components/' . $this::name_lower . '/');
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package and Namespace.');
    }


    /**
     * Update the model
     */
    protected function model()
    {
        if (empty($this->config['core'] . 'model/schema/' . $this::name_lower . '.mysql.schema.xml')) {
            return;
        }
        /** @var xPDOCacheManager $cache */
        if ($cache = $this->modx->getCacheManager()) {
            $cache->deleteTree(
                $this->config['core'] . 'model/' . $this::name_lower . '/mysql',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
        }

        /** @var xPDOManager $manager */
        $manager = $this->modx->getManager();
        /** @var xPDOGenerator $generator */
        $generator = $manager->getGenerator();
        $generator->parseSchema(
            $this->config['core'] . 'model/schema/' . $this::name_lower . '.mysql.schema.xml',
            $this->config['core'] . 'model/'
        );
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Model updated');
    }


    /**
     * Install nodejs and update assets
     */
    protected function assets()
    {
        if (!file_exists($this->config['build'] . 'node_modules')) {
            putenv('PATH=' . trim(shell_exec('echo $PATH')) . ':' . dirname(MODX_BASE_PATH) . '/');
            $this->modx->log(modX::LOG_LEVEL_INFO, 'Trying to install or update nodejs dependencies');
            $output = [
                shell_exec('cd ' . $this->config['build'] . ' && npm config set scripts-prepend-node-path true && npm install'),
                shell_exec('cd ' . $this->config['build'] . ' && npm link gulp'),
                shell_exec('cd ' . $this->config['build'] . ' && gulp copy'),
            ];
            $this->modx->log(xPDO::LOG_LEVEL_INFO, implode("\n", array_map('trim', $output)));
        }
        $output = shell_exec('cd ' . $this->config['build'] . ' && gulp js css 2>&1');
        $this->modx->log(xPDO::LOG_LEVEL_INFO, 'Compile scripts and styles ' . trim($output));
    }


    /**
     * Add settings
     */
    protected function settings()
    {
        /** @noinspection PhpIncludeInspection */
        $settings = include($this->config['elements'] . 'settings.php');
        if (!is_array($settings)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in settings');

            return;
        }
        $attributes = [
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => false,
        ];
        foreach ($settings as $name => $data) {
            /** @var modSystemSetting $setting */
            $setting = $this->modx->newObject('modSystemSetting');
            $setting->fromArray(array_merge([
                'key' => 'apptemplate_' . $name,
                'namespace' => $this::name_lower,
            ], $data), '', true, true);
            $vehicle = $this->builder->createVehicle($setting, $attributes);
            $this->builder->putVehicle($vehicle);
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' System Settings');
    }

    /**
     * Add resources
     */
    protected function resources()
    {
        /** @noinspection PhpIncludeInspection */
        $resources = include($this->config['elements'] . 'resources.php');
        if (!is_array($resources)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in Resources');

            return;
        }
        $attributes = [
            xPDOTransport::UNIQUE_KEY => 'id',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => false,
        ];

        $objects = [];
        foreach ($resources as $context => $items) {
            $menuindex = 0;
            foreach ($items as $alias => $item) {
                $item['id'] = $this->_idx++;
                $item['alias'] = $alias;
                $item['context_key'] = $context;
                $item['menuindex'] = $menuindex++;
                $objects = array_merge(
                    $objects,
                    $this->_addResource($item, $alias)
                );
            }
        }

        /** @var modResource $resource */
        foreach ($objects as $resource) {
            $vehicle = $this->builder->createVehicle($resource, $attributes);
            $this->builder->putVehicle($vehicle);
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($objects) . ' Resources');
    }


    /**
     * Add plugins
     */
    protected function plugins()
    {
        /** @noinspection PhpIncludeInspection */
        $plugins = include($this->config['elements'] . 'plugins.php');
        if (!is_array($plugins)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in Plugins');

            return;
        }

        $attributes = [
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
                'PluginEvents' => [
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
                ],
            ],
        ];

        foreach ($plugins as $name => $data) {
            /** @var modPlugin $plugin */
            $plugin = $this->modx->newObject('modPlugin');
            $plugin->fromArray([
                'name' => $name,
                'category' => 0,
                'description' => @$data['description'],
                'plugincode' => $this->_getContent($this->config['core'] . 'elements/plugins/' . $data['file'] . '.php'),
                'static' => false,
                'source' => 1,
                'static_file' => 'core/components/' . $this::name_lower . '/elements/plugins/' . $data['file'] . '.php',
            ], '', true, true);

            $events = [];
            if (!empty($data['events'])) {
                foreach ($data['events'] as $event_name => $event_data) {
                    /** @var modPluginEvent $event */
                    $event = $this->modx->newObject('modPluginEvent');
                    $event->fromArray(array_merge([
                        'event' => $event_name,
                        'priority' => 0,
                        'propertyset' => 0,
                    ], $event_data), '', true, true);
                    $events[] = $event;
                }
            }
            if (!empty($events)) {
                $plugin->addMany($events);
            }
            $vehicle = $this->builder->createVehicle($plugin, $attributes);
            $this->builder->putVehicle($vehicle);
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($plugins) . ' Plugins');
    }

    /**
     * Add ClientConfig
     */

    protected function clientconfig(){

        $tvs = preg_replace('/(\<\?php.*return)/s', '$configs =', file_get_contents($this->config['elements'] . 'clientconfig.php'));
        $resolver = file_get_contents(dirname(__FILE__)  . '/resolvers/clientconfig.php');
        $resolver = preg_replace('/(\$clientconfigs\s=.*?\]\;)/s', $tvs, $resolver);
        file_put_contents(dirname(__FILE__)  . '/resolvers/clientconfig.php', $resolver);

        $clientconfigs = include($this->config['elements'] . 'clientconfig.php');
        if(!is_array($clientconfigs)){
            $this->modx->log(modx::LOG_LEVEL_ERROR, 'Could not package clientconfig');
            return;
        }

        foreach ($clientconfigs as $key => $val) {
            // Проверяем, есть ли категория
            if(!$group = $this->modx->getObject('cgGroup', ['label' => $val['group']])){
                $ar = [
                    'label' => $val['group'],
                    'description' => '',
                    'sortorder' => ''
                ];
                $group = $this->modx->newObject('cgGroup');
                $group->fromArray($ar);
                $group->save();
            }
            $group = $group->get('id');
            $val = array_merge($val, [
                'group' => $group,
                'key' => $key
            ]);

            if(!$clientConfig = $this->modx->getObject('cgSetting', ['key' => $val['key']])){
                $save = $this->modx->newObject('cgSetting');
                $save->fromArray($val);
                $save->save();
            }else{
                foreach ($val as $k => $v) {
                    $clientConfig->set($k, $v);
                }
                $clientConfig->save();
            }

        }
    }


    /**
     * Add templates
     */
    protected function templates()
    {
        /** @noinspection PhpIncludeInspection */
        $templates = include($this->config['elements'] . 'templates.php');
        if (!is_array($templates)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in Templates');

            return;
        }

        $attributes = [
            xPDOTransport::UNIQUE_KEY => 'templatename',
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::RELATED_OBJECTS => false,
        ];

        foreach ($templates as $name => $data) {
            /** @var modTemplate $template */
            $template = $this->modx->newObject('modTemplate');
            $template->fromArray([
                'templatename' => $name,
                'description' => $data['description'],
                'content' => file_exists($this->config['core'] . "elements/templates/{$data['file']}.tpl")
                    ? "{include 'file:templates/{$data['file']}.tpl'}"
                    : '',
            ], '', true, true);
            $vehicle = $this->builder->createVehicle($template, $attributes);
            $this->builder->putVehicle($vehicle);
        }
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Packaged in ' . count($templates) . ' Templates');
    }


    /**
     * TV
     */
    protected function tv(){
        $tvs = preg_replace('/(\<\?php.*return)/s', '$tvs =', file_get_contents($this->config['elements'] . 'tv.php'));
        $resolver = file_get_contents(dirname(__FILE__)  . '/resolvers/tv.php');
        $resolver = preg_replace('/(\$tvs\s=.*?\]\;)/s', $tvs, $resolver);
        file_put_contents(dirname(__FILE__)  . '/resolvers/tv.php', $resolver);

        $tvs = include($this->config['elements'] . 'tv.php');
        foreach ($tvs as $name => $data) {
            if($data['templates'] && is_array($data['templates'])){
                $templates = [];
                foreach ($data['templates'] as $template) {
                    $temp = $this->_getTemplateId($template, true);
                    $templates['templates'][$temp['id']] = $temp;
                }
            }
            $data = array_merge(
                $data,
                $templates,
                ['name' => $name, 'category' => $this->_getCategoryId($data['category'])]
            );
            if($data['type'] == 'migx' && $data['inputProperties']){
                foreach ($data['inputProperties'] as $key => $val) {
                    $data['inopt_' . $key] = json_encode($val);
                }
            }
            $obTv = $this->modx->getObject('modTemplateVar', ['name' => $name]);
            if(is_object($obTv)){
                $data = array_merge(
                    $obTv->toArray(),
                    $data
                );
                $response = $this->modx->runProcessor('element/tv/update',$data);

            }else{
                $response = $this->modx->runProcessor('element/tv/create', $data);
            }

            $resp = $response->response;
            if($resp['success']){
                if($data['resources'] && is_array($data['resources'])){
                    foreach ($data['resources'] as $key => $val){
                        $resource = $this->modx->getObject('modResource',['alias' => $key]);
                        if(is_object($resource)){
                            $resource->setTVValue($data['name'], $val);
                            $resource->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * miniShop2 Options
     */
    protected function options(){

        $tvs = preg_replace('/(\<\?php.*return)/s', '$options =', file_get_contents($this->config['elements'] . 'options.php'));
        $resolver = file_get_contents(dirname(__FILE__)  . '/resolvers/options.php');
        $resolver = preg_replace('/(\$options\s=.*?\]\;)/s', $tvs, $resolver);
        file_put_contents(dirname(__FILE__)  . '/resolvers/options.php', $resolver);

        $options = include($this->config['elements'] . 'options.php');
        if(!is_array($options) && empty($options)) return;
        $processorsOptions = [
            'processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/mgr/'
        ];
        foreach ($options as $key => $val) {

            if(is_array($val['resources'])){
                $outCat = [];
                foreach ($val['resources'] as $category) {
                    $cat = $this->modx->getObject('modResource', ['alias' => $category]);
                    if(is_object($cat)){
                        $outCat[$cat->get('id')] = 1;
                    }
                }
            }
            $val['category'] = 0;
            $data = array_merge(
                ['key' => $key],
                $val,
                ['categories' => json_encode($outCat)]
            );
            if(!$this->modx->getCount('msOption', ['key' => $key])){
                $response = $this->modx->runProcessor('settings/option/create', $data, $processorsOptions);
            }else{
                $option = $this->modx->getObject('msOption', ['key' => $key]);
                $data['id'] = $option->get('id');
                $response = $this->modx->runProcessor('settings/option/update', $data, $processorsOptions);
            }
        }
    }

    /**
     * miniShop2 source
     */
    protected function source(){
        $sourcefile = preg_replace('/(\<\?php.*return)/s', '$source =', file_get_contents($this->config['elements'] . 'source.php'));
        $resolver = file_get_contents(dirname(__FILE__)  . '/resolvers/source.php');
        $resolver = preg_replace('/(\$properties\s=.*?\]\;)/s', $sourcefile, $resolver);
        file_put_contents(dirname(__FILE__)  . '/resolvers/source.php', $resolver);

        $properties = include($this->config['elements'] . 'source.php');
        /** @var $source modMediaSource */
        if (!$source = $this->modx->getObject('sources.modMediaSource', ['name' => $properties['name']])) {
            $source = $this->modx->newObject('sources.modMediaSource', $properties);
        } else {
            $default = $source->get('properties');
            foreach ($properties['properties'] as $k => $v) {
                if (!array_key_exists($k, $default)) {
                    $default[$k] = $v;
                }
            }
            $source->set('properties', $default);
        }
        $source->save();

        if ($setting = $this->modx->getObject('modSystemSetting', ['key' => 'ms2_product_source_default'])) {
            if (!$setting->get('value')) {
                $setting->set('value', $source->get('id'));
                $setting->save();
            }
        }
        @mkdir(MODX_ASSETS_PATH . 'images/');
        @mkdir(MODX_ASSETS_PATH . 'images/products/');

        // Удаляем пустые источники
        $sources = $this->modx->getIterator('sources.modMediaSource', ['name' => '']);
        if(is_object($sources)){
            foreach ($sources as $source) {
                print_r($source->toArray());
                $source->remove();
            }
        }
    }


    /**
     *  Install package
     */
    protected function install()
    {
        $signature = $this->builder->getSignature();
        $sig = explode('-', $signature);
        $versionSignature = explode('.', $sig[1]);

        /** @var modTransportPackage $package */
        if (!$package = $this->modx->getObject('transport.modTransportPackage', ['signature' => $signature])) {
            $package = $this->modx->newObject('transport.modTransportPackage');
            $package->set('signature', $signature);
            $package->fromArray([
                'created' => date('Y-m-d h:i:s'),
                'updated' => null,
                'state' => 1,
                'workspace' => 1,
                'provider' => 0,
                'source' => $signature . '.transport.zip',
                'package_name' => $this::name,
                'version_major' => $versionSignature[0],
                'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
                'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
            ]);
            if (!empty($sig[2])) {
                $r = preg_split('#([0-9]+)#', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
                if (is_array($r) && !empty($r)) {
                    $package->set('release', $r[0]);
                    $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
                } else {
                    $package->set('release', $sig[2]);
                }
            }
            $package->save();
        }
        if ($package->install()) {
            $this->modx->runProcessor('system/clearcache');
        }
    }


    /**
     * @param bool $install
     *
     * @return modPackageBuilder
     */
    public function process($install = true)
    {
        ob_start();
        $this->model();
        //$this->assets();

        // Add elements
        $elements = scandir($this->config['elements']);
        foreach ($elements as $element) {
            if (in_array($element[0], ['_', '.'])) {
                continue;
            }
            $name = preg_replace('#\.php$#', '', $element);
            if (method_exists($this, $name)) {
                $this->{$name}();
            }
        }

        // Create main vehicle
        $vehicle = $this->builder->createVehicle([
            'source' => $this->config['core'],
            'target' => "return MODX_CORE_PATH . 'components/';",
        ], [
            'vehicle_class' => 'xPDOFileVehicle',
        ]);
        $vehicle->resolve('file', [
            'source' => $this->config['assets'],
            'target' => "return MODX_ASSETS_PATH . 'components/';",
        ]);

        // Add resolvers into vehicle
        $resolvers = scandir($this->config['resolvers']);
        foreach ($resolvers as $resolver) {
            if (in_array($resolver[0], ['_', '.'])) {
                continue;
            }
            if ($vehicle->resolve('php', ['source' => $this->config['resolvers'] . $resolver])) {
                $this->modx->log(modX::LOG_LEVEL_INFO, 'Added resolver ' . $name = preg_replace('#\.php$#', '', $resolver));
            }
        }
        $this->builder->putVehicle($vehicle);

        $this->builder->setPackageAttributes([
            'changelog' => file_get_contents($this->config['core'] . 'docs/changelog.txt'),
            'license' => file_get_contents($this->config['core'] . 'docs/license.txt'),
            'readme' => file_get_contents($this->config['core'] . 'docs/readme.txt'),
        ]);
        $this->modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');

        $this->modx->log(modX::LOG_LEVEL_INFO, 'Packing up transport package zip...');
        $this->builder->pack();

        if ($install) {
            $this->install();
        }

        return $this->builder;
    }


    /**
     * @param $filename
     *
     * @return string
     */
    protected function _getContent($filename)
    {
        $file = trim(file_get_contents($filename));
        preg_match('#\<\?php(.*)#is', $file, $data);

        return rtrim(rtrim(trim($data[1]), '?>'));
    }


    /**
     * @param array $data
     * @param string $uri
     * @param int $parent
     *
     * @return array
     */
    protected function _addResource(array $data, $uri, $parent = 0)
    {
        $file = $data['context_key'] . '/' . $uri;
        $template = $data['template'] = $this->_getTemplateId($data['template']);

        if($data['tv'] && is_array($data['tv'])){
            $data['tvs'] = true;
            foreach ($data['tv'] as $key => $val){
                $tv = $this->modx->getObject('modTemplateVar', ['name' => $key]);
                if(is_object($tv)){
                    $data['tv' . $tv->get('id')] = $val;
                }
            }
        }

        if($data['class_key'] == 'msProduct'){
            $data['show_in_tree'] = false;
        }

        /** @var modResource $resource */
        $resource = $this->modx->newObject('modResource');
        $resource->fromArray(array_merge([
            'parent' => $parent,
            'published' => true,
            'deleted' => false,
            'hidemenu' => false,
            'createdon' => time(),
            'template' => $template,
            'isfolder' => !empty($data['isfolder']) || !empty($data['resources']),
            'uri' => $uri,
            'uri_override' => false,
            'richtext' => true,
            'searchable' => true,
            'content' => file_exists($this->config['core'] . "elements/resources/{$file}.tpl")
                ? file_get_contents($this->config['core'] . "elements/resources/{$file}.tpl")
                : '',
        ], $data), '', true, true);

        if (!empty($data['groups'])) {
            foreach ($data['groups'] as $group) {
                $resource->joinGroup($group);
            }
        }
        $resources[] = $resource;

        if (!empty($data['resources'])) {
            $menuindex = 0;
            foreach ($data['resources'] as $alias => $item) {
                $item['id'] = $this->_idx++;
                $item['alias'] = $alias;
                $item['context_key'] = $data['context_key'];
                $item['menuindex'] = $menuindex++;
                $resources = array_merge(
                    $resources,
                    $this->_addResource($item, $uri . '/' . $alias, $data['id'])
                );


            }
        }

        return $resources;
    }

    /**
     * @param $templateName
     * @return int
     */
    protected function _getTemplateId($templateName, $full = false){
        if(!$templateName){
            return 0;
        }

        $template = $this->modx->getObject('modTemplate', ['templatename' => $templateName]);
        if($templateName == null) return 0;
        if($full !== false){
            return array_merge($template->toArray(), ['access' => true]);
        }

        return is_object($template) ? $template->get('id') : 0;
    }

    /**
     * @param $categoryName
     */
    protected function _getCategoryId($categoryName){
        $obCategory = $this->modx->getObject('modCategory', ['category' => $categoryName]);
        if(!is_object($obCategory)){
            $response = $this->modx->runProcessor('element/category/create',[
                'parent' => 0,
                'category' => $categoryName,
                'rank' => 0
            ]);

            if($response->isError()){
                return false;
            }
            return $response->response['object']['id'];
        }

        $id = $obCategory->get('id');
        return $id;
    }



}

$core = dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
if (!file_exists($core)) {
    exit('Could not load config core!');
}
/** @noinspection PhpIncludeInspection */
require $core;
$install = new AppTemplatePackage(MODX_CORE_PATH);
$builder = $install->process(true);

if (!empty($_GET['download'])) {
    $signature = $builder->getSignature();
    echo '<script>document.location.href = "/core/packages/' . $signature . '.transport.zip' . '";</script>';
}