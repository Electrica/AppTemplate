<?php

return [
    'web' => [
        'index' => [
            'pagetitle' => 'Главная',
            'template' => 'MainTemplate',
            'hidemenu' => false,
        ],
        'catalog' => [
            'pagetitle' => 'Каталог',
            'template' => 'CatalogTemplate',
            'class_key' => 'msCategory',
            'hidemenu' => false,
            'published' => true,
            'resources' => [
                'phone' => [
                    'pagetitle' => 'Телефоны',
                    'template' => 'CatalogTemplate',
                    'class_key' => 'msCategory',
                    'hidemenu' => false,
                    'published' => true,
                    'resources' => [
                        'iphone' => [
                            'pagetitle' => 'Телефон IPhone',
                            'template' => 'ProductTemplate',
                            'class_key' => 'msProduct',
                            'hidemenu' => false,
                            'published' => true,
                        ]
                    ]
                ],
                'television' => [
                    'pagetitle' => 'Телевизоры',
                    'template' => 'CatalogTemplate',
                    'class_key' => 'msCategory',
                    'hidemenu' => false,
                    'published' => true,
                ]
            ]
        ],
        'service' => [
            'pagetitle' => 'Service',
            'template' => '',
            'hidemenu' => true,
            'published' => false,
            'resources' => [
                '404' => [
                    'pagetitle' => '404',
                    'template' => '',
                    'hidemenu' => true,
                    'uri' => '404',
                    'uri_override' => true,
                ],
                'sitemap.xml' => [
                    'pagetitle' => 'Sitemap',
                    'template' => 0,
                    'hidemenu' => true,
                    'uri' => 'sitemap.xml',
                    'uri_override' => true,
                ],
            ],
        ],
    ],
];