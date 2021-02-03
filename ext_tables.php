<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

ExtensionManagementUtility::addStaticFile('gatedcontent', 'Configuration/TypoScript', 'Gated content');
ExtensionManagementUtility::addLLrefForTCAdescr(
    'tx_gatedcontent_domain_model_userdata',
    'EXT:gatedcontent/Resources/Private/Language/locallang_csh_tx_gatedcontent_domain_model_userdata.xlf'
);
ExtensionManagementUtility::allowTableOnStandardPages('tx_gatedcontent_domain_model_userdata');
