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
        'title'          => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_blogseries',
        'label'          => 'title',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'cruser_id'      => 'cruser_id',
        'dividers2tabs'  => true,
        'default_sortby' => 'title ASC',
        'delete'         => 'deleted',
        'enablecolumns'  => [
            'disabled' => 'hidden'
        ],
        'searchFields'   => 'title',
        'iconfile'       => 'EXT:tw_blog/Resources/Public/Icons/Extension/BlogSeries.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title, description',
    ],
    'types'     => [
        '1' => [
            'showitem' =>
                'title,
                description,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden'
        ],
    ],
    'columns'   => [
        'hidden'      => [
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
        'title'       => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_blogseries.title',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:tx_twblog_domain_model_blogseries.description',
            'config'  => [
                'type'           => 'text',
                'cols'           => 80,
                'rows'           => 15,
                'softref'        => 'typolink_tag,email[subst],url',
                'enableRichtext' => true,
            ],
        ],
    ],
];
