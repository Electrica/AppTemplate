<?php
$resources = [
    'web' => [
        'index' => [
            'pagetitle' => 'Главная',
            'template' => 'MainTemplate',
            'hidemenu' => false
        ],
        'catalog' => [
            'pagetitle' => 'Каталог',
            'template' => 'AllCatalogTemplate',
            'class_key' => 'msCategory',
            'hidemenu' => true,
            'published' => true,
            'resources' => [
                'Summer-shirts' => [
                    'pagetitle' => 'Летние рубашки',
                    'template' => 'CatalogTemplate',
                    'class_key' => 'msCategory',
                    'longtitle' => 'Этот заголовок выводится на главной',
                    'description' => 'Это тоже выведется на главной',
                    'hidemenu' => true,
                    'published' => true,
                    'resources' => [
                        'unisex-shirts' => [
                            'pagetitle' => 'Универсальные рубашки',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                            'resources' => [
                                'shirts' => [
                                    'pagetitle' => 'Рубашка',
                                    'template' => 'ProductTemplate',
                                    'class_key' => 'msProduct',
                                    'hidemenu' => true,
                                    'published' => true,
                                    'price' => 2500,
                                    'old_price' => 2700,
                                    'introtext' => 'Тут текст маленький для вывода в товаре',
                                    'content' => '<p>Тут уже большой текст описание для полного текста</p>',
                                    'article' => 152365565
                                ]
                            ]
                        ],
                        'casual-shirts' => [
                            'pagetitle' => 'Современные рубашки',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                            'resources' => [
                                'shirts-cas' => [
                                    'pagetitle' => 'Рубашка с современным оборотом',
                                    'template' => 'ProductTemplate',
                                    'class_key' => 'msProduct',
                                    'hidemenu' => true,
                                    'published' => true,
                                    'price' => 2500,
                                    'new' => 1
                                ]
                            ]
                        ],
                        'man-shirts' => [
                            'pagetitle' => 'Мужские рубашки',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                        ],
                        'woman-shirts' => [
                            'pagetitle' => 'Женские рубашки',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                        ],
                        'woman-shoes' => [
                            'pagetitle' => 'Женские туфли',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                        ],
                        'man-shoes' => [
                            'pagetitle' => 'Мужские туфли',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                        ],
                    ]
                ],
                'Winter-jackets' => [
                    'pagetitle' => 'Зимние жакеты',
                    'template' => 'CatalogTemplate',
                    'class_key' => 'msCategory',
                    'longtitle' => 'Этот заголовок выводится на главной',
                    'description' => 'Это тоже выведется на главной',
                    'hidemenu' => true,
                    'published' => true,
                    'resources' => [
                        'modern-jaket' => [
                            'pagetitle' => 'Современный жакет',
                            'template' => 'CatalogTemplate',
                            'class_key' => 'msCategory',
                            'hidemenu' => true,
                            'published' => true,
                        ]
                    ]
                ]
            ]
        ],
        'about' => [
            'pagetitle' => 'О нас',
            'template' => 'ContentTemplate',
            'hidemenu' => false,
            'published' => true,
        ],
        'delivery' => [
            'pagetitle' => 'Доставка',
            'template' => 'ContentTemplate',
            'hidemenu' => false,
            'published' => true,
        ],
        'payment' => [
            'pagetitle' => 'Оплата',
            'template' => 'ContentTemplate',
            'hidemenu' => false,
            'published' => true,
        ],
        'more-link' => [
            'pagetitle' => 'Выпадающий',
            'template' => 'EmptyTemplate',
            'hidemenu' => false,
            'published' => true,
            'resources' => [
                'more-link-one' => [
                    'pagetitle' => 'Выпадающий один',
                    'template' => 'EmptyTemplate',
                    'hidemenu' => false,
                    'published' => true,
                ],
                'more-link-two' => [
                    'pagetitle' => 'Выпадающий два',
                    'template' => 'EmptyTemplate',
                    'hidemenu' => false,
                    'published' => true,
                ]
            ]
        ],
        'cart' => [
            'pagetitle' => 'Корзина',
            'template' => 'CartTemplate',
            'hidemenu' => true,
            'published' => true,
        ],
        'search' => [
            'pagetitle' => 'Поиск',
            'template' => 'SearchTemplate',
            'hidemenu' => true,
            'published' => true,
        ],
        'contact' => [
            'pagetitle' => 'Контакты',
            'template' => 'ContactTemplate',
            'hidemenu' => false,
            'published' => true,
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
                    'richtext' => false,
                ],
            ],
        ],
    ],
];
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

            if($resources){
                foreach ($resources as $context => $items){
                    // Выбираем ресурс по alias
                    foreach ($items as $alias => $item){
                        _setTemplate($item, $alias, $modx);
                    }
                }
            }

            break;
    }

}

function _setTemplate($item, $alias, $modx){
    /**
     * @var modX $modx
     */
    $resource = $modx->getObject('modResource', ['alias' => $alias]);
    if(is_object($resource)){
        $template = $modx->getObject('modTemplate', ['templatename' => $item['template']]);
        if(is_object($template)){
            $resource->set('template', $template->get('id'));
            $resource->save();
        }
        if($item['resources']){
            foreach ($item['resources'] as $alias => $item){
                _setTemplate($item, $alias, $modx);
            }
        }
    }
}

return true;