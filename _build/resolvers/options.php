<?php
$options = [
    'test_option' => [
        'caption' => 'Тестовая опция',
        'description' => 'Описание тестовой опции',
        'measure_unit' => 'Кв/час',
        'type' => 'combo-boolean',
        'resources' => [
            'catalog',
            'phone'
        ]
    ],
    'voltage' => [
        'caption' => 'Вольтажность',
        'description' => 'Описание тестовой опции',
        'measure_unit' => 'Кв/час',
        'type' => 'textfield',
        'categories' => [
            'CatalogTemplate'
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

            $processorsOptions = [
                'processors_path' => MODX_CORE_PATH . 'components/minishop2/processors/mgr/'
            ];
            foreach ($options as $key => $val) {

            if(is_array($val['resources'])){
                $outCat = [];
                foreach ($val['resources'] as $category) {
                    $cat = $modx->getObject('modResource', ['alias' => $category]);
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
            if(!$modx->getCount('msOption', ['key' => $key])){
                $response = $modx->runProcessor('settings/option/create', $data, $processorsOptions);
            }else{
                $option = $modx->getObject('msOption', ['key' => $key]);
                $data['id'] = $option->get('id');
                $response = $modx->runProcessor('settings/option/update', $data, $processorsOptions);
            }
        }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;