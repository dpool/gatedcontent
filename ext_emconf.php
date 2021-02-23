<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "gatedcontent"
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['gatedcontent'] = array(
    'title' => 'Gatedcontent',
    'description' => 'Gatedcontent is lead-capture extension for TYPO3. The extension provides editors with a configurable form element to collect user data. In exchange for personal information like e-mail address, name, company etc., visitors can access downloads or a single TYPO3 page.',
    'category' => 'plugin',
    'author' => 'Daniel Thomas, Andreas Habel, Andrea Hildebrand, Markus Winter',
    'author_email' => 'dev@dpool.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '1',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4.0 - 10.4.99'
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
