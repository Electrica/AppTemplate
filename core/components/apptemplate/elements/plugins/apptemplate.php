<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var AppTemplate $AppTemplate */
switch ($modx->event->name) {
    case 'OnMODXInit':
        if ($AppTemplate = $modx->getService('AppTemplate', 'AppTemplate', MODX_CORE_PATH . 'components/apptemplate/model/')) {
            $AppTemplate->initialize();
        }
        break;
    default:
        if ($AppTemplate = $modx->getService('AppTemplate')) {
            $AppTemplate->handleEvent($modx->event, $scriptProperties);
        }
}