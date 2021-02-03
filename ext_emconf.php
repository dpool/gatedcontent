<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "gatedcontent"
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF['gatedcontent'] = array(
    'title' => 'Gatedcontent',
    'description' => 'Gatedcontent',
    'category' => 'plugin',
    'author' => 'Daniel Thomas, Andreas Habel, Andrea Hildebrand, Markus Winter',
    'author_email' => 'dev@dpool.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '1',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.1',
    'constraints' => array(
        'depends' => array(
            'typo3' => '10.4.0 - 10.4.99'
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
);
