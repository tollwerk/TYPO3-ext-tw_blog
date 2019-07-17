<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "tw_blog"
 *
 * Auto generated by the Tollwerk Yeoman Generator (https://github.com/tollwerk/generator-tollwerk)
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title'            => 'tollwerk Blog',
    'description'      => 'Blog plugins & backend tools',
    'category'         => 'misc',
    'author'           => 'tollwerk GmbH',
    'author_email'     => 'info@tollwerk.de',
    'state'            => 'alpha',
    'internal'         => '',
    'uploadfolder'     => '0',
    'clearCacheOnLoad' => 0,
    'version'          => '1.0.0',
    'createDirs'       => 'fileadmin/user_upload/blog',
    'constraints'      => [
        'depends'   => [
            'typo3' => '9.5.0-9.99.99',
            'vhs'   => '5.0.0-',
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];
