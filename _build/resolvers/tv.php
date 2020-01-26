<?php
$tvs = [
    'show_main' => [
        'type' => 'checkbox',
        'els' => 'Да==1',
        'caption' => 'Показывать на главной',
        'description' => 'Описание ТВ',
        'category' => 'Главная',
        'templates' => [
            'MainTemplate',
            'CatalogTemplate'
        ],
        'resources' => [
            'index' => true,
            'catalog' => true
        ]
    ],
    'banner' => [
        'type' => 'image',
        'caption' => 'Картинка',
        'description' => 'Картинка для главной',
        'category' => 'Баннер',
        'templates' => [
            'MainTemplate',
            'CatalogTemplate'
        ],
        'resources' => [
            'index' => 'assets/image.jpg',
            'catalog' => 'assets/catalog.png',
        ]
    ],
    'blocks' => [
        'type' => 'migx',
        'caption' => 'Блоки',
        'description' => 'Описание ТВ',
        'category' => 'Блоки на главной',
        'inputProperties' => [
            'formtabs' => [
                [
                    'caption' => 'Блоки',
                    'fields' => [
                        [
                            'field' => 'block_title',
                            'caption' => 'Заголовок'
                        ],
                        [
                            'field' => 'block_description',
                            'caption' => 'Описание'
                        ],
                        [
                            'field' => 'block_image',
                            'caption' => 'Картинка',
                            'inputTVtype' => 'image'
                        ]
                    ]
                ]
            ],
            'columns' => [
                [
                    'header' => 'Картинка',
                    'dataIndex' => 'block_image',
                    'renderer' => 'this.renderImage'
                ],
                [
                    'header' => 'Заголовок',
                    'dataIndex' => 'block_title'
                ],
                [
                    'header' => 'Описание',
                    'dataIndex' => 'block_description'
                ]
            ]
        ],
        'templates' => [
            'MainTemplate'
        ]
    ]
];


/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            foreach ($tvs as $name => $data) {
            if($data['templates'] && is_array($data['templates'])){
                $templates = [];
                foreach ($data['templates'] as $template) {
                    $temp = _getTemplateId($template, true, $modx);
                    $templates['templates'][$temp['id']] = $temp;
                }
            }
            $data = array_merge(
                $data,
                $templates,
                ['name' => $name, 'category' => _getCategoryId($data['category'], $modx)]
            );
            if($data['type'] == 'migx' && $data['inputProperties']){
                foreach ($data['inputProperties'] as $key => $val) {
                    $data['inopt_' . $key] = json_encode($val);
                }
            }
            $obTv = $modx->getObject('modTemplateVar', ['name' => $name]);
            if(is_object($obTv)){
                $data = array_merge(
                    $obTv->toArray(),
                    $data
                );
                $response = $modx->runProcessor('element/tv/update',$data);

            }else{
                $response = $modx->runProcessor('element/tv/create', $data);
            }

            $resp = $response->response;
            if($resp['success']){
                if($data['resources'] && is_array($data['resources'])){
                    foreach ($data['resources'] as $key => $val){
                        $resource = $modx->getObject('modResource',['alias' => $key]);
                        if(is_object($resource)){
                            $resource->setTVValue($data['name'], $val);
                            $resource->save();
                        }
                    }
                }
            }
        }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;

function _getTemplateId($templateName, $full = false, $modx){
    if(!$templateName){
        return 0;
    }

    $template = $modx->getObject('modTemplate', ['templatename' => $templateName]);
    if($templateName == null) return 0;
    if($full !== false){
        return array_merge($template->toArray(), ['access' => true]);
    }

    return is_object($template) ? $template->get('id') : 0;
}

function _getCategoryId($categoryName, $modx){
    $obCategory = $modx->getObject('modCategory', ['category' => $categoryName]);
    if(!is_object($obCategory)){
        $response = $modx->runProcessor('element/category/create',[
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