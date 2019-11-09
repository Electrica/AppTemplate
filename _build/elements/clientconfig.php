<?php

return [
    'phone' => [
        'label' => 'Телефон',
        'xtype' => 'textfield',
        'value' => '+7 771 777 77 77',
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