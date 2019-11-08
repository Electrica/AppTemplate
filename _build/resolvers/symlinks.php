<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/AppTemplate/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/apptemplate')) {
            $cache->deleteTree(
                $dev . 'assets/components/apptemplate/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/apptemplate/', $dev . 'assets/components/apptemplate');
        }
        if (!is_link($dev . 'core/components/apptemplate')) {
            $cache->deleteTree(
                $dev . 'core/components/apptemplate/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/apptemplate/', $dev . 'core/components/apptemplate');
        }
    }
}

return true;