<?php
$configs = [
    'phone' => [
        'label' => 'Телефон',
        'xtype' => 'textfield',
        'value' => '+7 888 888 88 88',
        'group' => 'Контакты',
        'description' => 'Введите пожалуйста телефон'
    ],
    'address' => [
        'label' => 'Адрес',
        'xtype' => 'textfield',
        'value' => 'Алматы, улица какая то',
        'group' => 'Адрес',
        'description' => ''
    ],
    'xtype-test' => [
        'label' => 'Тестовый для xtype',
        'xtype' => 'modx-combo',
        'options' => 'First||Second||Third||Fourth',
        'group' => 'Икс комбо',
        'description' => ''
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

            $path = $modx->getOption('clientconfig.core_path', null, $modx->getOption('core_path') . 'components/clientconfig/');
            $path .= 'model/clientconfig/';
            $clientConfig = $modx->getService('clientconfig','ClientConfig', $path);

            foreach ($clientconfigs as $key => $val) {
            // Проверяем, есть ли категория
            if(!$group = $modx->getObject('cgGroup', ['label' => $val['group']])){
                $ar = [
                    'label' => $val['group'],
                    'description' => '',
                    'sortorder' => ''
                ];
                $group = $modx->newObject('cgGroup');
                $group->fromArray($ar);
                $group->save();
            }
            $group = $group->get('id');
            $val = array_merge($val, [
                'group' => $group,
                'key' => $key
            ]);
            if(!$clientConfig = $modx->getObject('cgSetting', ['key' => $val['key']])){
                $save = $modx->newObject('cgSetting');
                foreach($val as $k => $v){
                    $save->set($k, $v);
                }
                $save->save();
            }else{
                foreach ($val as $k => $v) {
                    $clientConfig->set($k, $v);
                }
                $clientConfig->save();
            }

        }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;