<?php

return [
    'show-main' => [
        'type' => 'textfield',
        'caption' => 'Показывать на главной',
        'description' => 'Описание ТВ',
        'category' => 'Главная',
        'templates' => [
            'MainTemplate',
            'BaseTemplate'
        ]
    ],
    'banner' => [
        'type' => 'textfield',
        'caption' => 'Ссылка',
        'description' => 'Описание ТВ',
        'category' => 'Баннер',
        'templates' => [
            'MainTemplate'
        ]
    ]
];