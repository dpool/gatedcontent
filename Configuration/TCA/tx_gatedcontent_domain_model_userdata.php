<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'first_name,last_name,company,telephone,email,zip,city,',
        'dynamicConfigFile' => ExtensionManagementUtility::extPath('gatedcontent') . 'Configuration/TCA/tx_gatedcontent_domain_model_userdata.php',
        'iconfile' => ExtensionManagementUtility::extPath('gatedcontent') . 'Resources/Public/Icons/tx_gatedcontent_domain_model_userdata.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, identifier, first_name, last_name, company, telephone, email, zip, city, newsletter_subscription',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, identifier, first_name, last_name, company, telephone, email, zip, city, newsletter_subscription, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_gatedcontent_domain_model_userdata',
                'foreign_table_where' => 'AND tx_gatedcontent_domain_model_userdata.pid=###CURRENT_PID### AND tx_gatedcontent_domain_model_userdata.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'identifier' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.identifier',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ]
        ],
        'first_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.first_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ]
        ],
        'last_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.last_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ]
        ],
        'company' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.company',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ],
        ],
        'telephone' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.telephone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ],
        ],
        'email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ],
        ],
        'zip' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.zip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ],
        ],
        'city' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.city',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255
            ],
        ],
        'newsletter_subscription' => [
            'exclude' => true,
            'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_domain_model_userdata.newsletter_subscription',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => ''
                    ],
                ],
            ]
        ]
    ],
];
