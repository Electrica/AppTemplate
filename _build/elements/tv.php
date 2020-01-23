<?php

return [
    'show_main' => [
        'type' => 'checkbox',
        'els' => 'Да==1',
        'caption' => 'Показывать на главной',
        'description' => 'Описание ТВ',
        'category' => 'Главная',
        'templates' => [
            'MainTemplate'
        ]
    ],
    'banner' => [
        'type' => 'checkbox',
        'caption' => 'Ссылка',
        'description' => 'Описание ТВ',
        'category' => 'Баннер',
        'templates' => [
            'MainTemplate'
        ]
    ],
    'blocks' => [
        'type' => 'migx',
        //'inopt_configs' => 'Конфигурация',
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