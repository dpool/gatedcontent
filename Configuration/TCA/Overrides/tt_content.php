<?php

/**
 * Add new fields to tt_content
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$temporaryColumns = array(
    'tx_gatedcontent_header' => [
        'exclude' => true,
        'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_header',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],
    'tx_gatedcontent_subheader' => [
        'exclude' => true,
        'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_subheader',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ],
    ],
    'tx_gatedcontent_description' => [
        'exclude' => true,
        'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_description',
        'config' => [
            'type' => 'text',
            'cols' => 30,
            'rows' => 5,
            'enableRichtext' => true,
        ],
    ],
    'tx_gatedcontent_image' => [
        'exclude' => true,
        'label' => 'LLL:EXT:gatedcontent/Resources/Private/Language/locallang_db.xlf:tx_gatedcontent_image',
        'config' => ExtensionManagementUtility::getFileFieldTCAConfig('tx_gatedcontent_image', [
            'appearance' => [
                'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
            ],
            // custom configuration for displaying fields in the overlay/reference table
            // to use the imageoverlayPalette instead of the basicoverlayPalette
            'overrideChildTca' => [
                'types' => [
                    '0' => [
                        'showitem' => '
                                --palette--;;imageoverlayPalette,
                                --palette--;;filePalette'
                    ],
                    File::FILETYPE_TEXT => [
                        'showitem' => '
                                --palette--;;imageoverlayPalette,
                                --palette--;;filePalette'
                    ],
                    File::FILETYPE_IMAGE => [
                        'showitem' => '
                                --palette--;;imageoverlayPalette,
                                --palette--;;filePalette'
                    ],
                    File::FILETYPE_AUDIO => [
                        'showitem' => '
                                --palette--;;audioOverlayPalette,
                                --palette--;;filePalette'
                    ],
                    File::FILETYPE_VIDEO => [
                        'showitem' => '
                                --palette--;;videoOverlayPalette,
                                --palette--;;filePalette'
                    ],
                    File::FILETYPE_APPLICATION => [
                        'showitem' => '
                                --palette--;;imageoverlayPalette,
                                --palette--;;filePalette'
                    ]
                ],
            ],
        ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])
    ]
);
ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $temporaryColumns
);

/**
 * Register plugin
 */
ExtensionUtility::registerPlugin(
    'gatedcontent',
    'gate',
    'Gated content'
);

$pluginSignature = 'gatedcontent_gate';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'tx_gatedcontent_header,tx_gatedcontent_subheader,tx_gatedcontent_description,tx_gatedcontent_image,pi_flexform';

ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:gatedcontent/Configuration/FlexForms/Gate.xml'
);
