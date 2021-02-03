<?php

declare(strict_types=1);

namespace Dpool\Gatedcontent\Tests\Acceptance\Support\Extension;

/*
 * This file is part of TYPO3 CMS-based extension "container" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

class BackendContainerEnvironment extends BackendEnvironment
{
    /**
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'core',
            'extbase',
            'fluid',
            'backend',
            'about',
            'install',
            'frontend',
            'recordlist',
            'fluid_styled_content',
            'cms-rte-ckeditor'
        ],
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/gatedcontent/Build/sites' => 'typo3conf/sites'
        ],
        'testExtensionsToLoad' => [
            'typo3conf/ext/gatedcontent'
        ],
        'configurationToUseInTestInstance' => [
            'BE' => ['debug' => '1'],
            'FE' => ['debug' => '1'],
            'SYS' => [
                'features' => ['fluidBasedPageModule' => false],
                'displayErrors' => '1',
                'devlPmask' => '*',
                'exceptionalErrors' => '12290',
                'encryptionKey' => 'secret',
            ],
            'LOG' => [
                'TYPO3' => [
                    'deprecations' => [
                        'writerConfiguration' => [
                            'notice' => [
                                'TYPO3\CMS\Core\Log\Writer\FileWriter' => ['disabled' => ''],
                            ]
                        ]
                    ]
                ]
            ],
            'MAIL' => [
                'transport' => 'smtp',
                'transport_smtp_server' => 'mailhog:1025',
            ],
        ],
        'xmlDatabaseFixtures' => [
            'EXT:gatedcontent/Tests/Fixtures/pages.xml',
            'EXT:gatedcontent/Tests/Fixtures/tt_content.xml',
            'EXT:gatedcontent/Tests/Fixtures/fe_groups.xml',
            'EXT:gatedcontent/Tests/Fixtures/sys_template.xml'
        ],
    ];
}
