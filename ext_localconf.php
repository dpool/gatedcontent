<?php

use Dpool\Gatedcontent\Service\TokenAuthService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

ExtensionUtility::configurePlugin(
    'Dpool.gatedcontent',
    'gate',
    array(
        'Gate' => 'form, processForm, handleDoubleOptIn, handleAdminConfirmation, deliverGatedContent',
    ),
    // non-cacheable actions
    array(
        'Gate' => 'form, processForm, handleDoubleOptIn, handleAdminConfirmation, deliverGatedContent',
    )
);

// Use a custom cache to invalidate double opt-in tokens.
// See Controller\GateController::handleDoubleOptInAction().
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tokencache'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tokencache'] = [];
}

// Add the authentication service.
ExtensionManagementUtility::addService(
    'gatedcontent',
    'auth',
    TokenAuthService::class,
    [
        'title' => 'JWT authentication',
        'description' => 'Authenticates users by a JSON Web Token',
        'subtype' => 'getUserFE,authUserFE',
        'available' => true,
        'priority' => 80,
        'quality' => 80,
        'os' => '',
        'exec' => '',
        'className' => TokenAuthService::class
    ]
);
