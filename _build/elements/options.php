<?php

return [
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