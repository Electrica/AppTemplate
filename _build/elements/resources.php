<?php

return [
    'web' => [
        'index' => [
            'pagetitle' => 'Home',
            'template' => 'MainTemplate',
            'hidemenu' => false,
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