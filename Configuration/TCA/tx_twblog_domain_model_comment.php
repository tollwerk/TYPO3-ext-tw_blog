<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @author     Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2019 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2019 Joschi Kuphal <joschi@tollwerk.de>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

return [
    'ctrl'      => [
        'hideTable'      => 1,
        'title'          => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment',
        'label'          => 'text',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'cruser_id'      => 'cruser_id',
        'dividers2tabs'  => true,
        'default_sortby' => 'uid ASC',
        'delete'         => 'deleted',
        'enablecolumns'  => [
            'disabled' => 'hidden',
        ],
        'searchFields'   => 'name,email,text,replies',
        'iconfile'       => 'EXT:tw_blog/Resources/Public/Icons/Extension/Comment.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title',
    ],
    'types'     => [
        '1' => [
            'showitem' =>
                '--palette--;;user,
                text,
                 --div--;LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:plugin.blog.comments,
                replies'
        ],
    ],
    'palettes'  => [
        'user' => ['showitem' => 'name, email, url'],
    ],
    'columns'   => [
        'crdate' => [
          'config' => [
            'type' => 'passthrough',
          ],
        ],
        'hidden'       => [
            'exclude' => true,
            'label'   => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config'  => [
                'type'       => 'check',
                'renderType' => 'checkboxToggle',
                'items'      => [
                    [
                        0                    => '',
                        1                    => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],
        'name'         => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment.name',
            'config'  => [
                'type'      => 'input',
                'size'      => 30,
                'eval'      => 'trim',
                'adminOnly' => true,
            ],
        ],
        'email'        => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment.email',
            'config'  => [
                'type'      => 'input',
                'size'      => 30,
                'eval'      => 'trim',
                'adminOnly' => true,
            ],
        ],
        'url'          => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment.url',
            'config'  => [
                'type'      => 'input',
                'size'      => 30,
                'eval'      => 'trim',
                'adminOnly' => true,
            ],
        ],
        'text'         => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment.text',
            'config'  => [
                'type'      => 'text',
                'cols'      => 40,
                'rows'      => 10,
                'eval'      => 'trim',
                'adminOnly' => true
            ],
        ],
        'replies'      => [
            'exclude'   => 0,
            'label'     => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_comment.replies',
            'l10n_mode' => 'exclude',
            'config'    => [
                'type'                => 'inline',
                'foreign_table'       => 'tx_twblog_domain_model_comment',
                'foreign_field'       => 'parent',
                'foreign_table_field' => 'parent_table',
                'maxitems'            => 999,
                'appearance'          => [
                    'collapseAll'  => 1,
                    'expandSingle' => 0,
                ],
                'overrideChildTca'    => [
                    'columns' => [
                        'replies' => [
                            'displayCond' => 'FIELD:usertype:=:-1',
                        ],
                    ],
                ],
            ],
        ],
        'parent'       => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'parent_table' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
    ],
];
