<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @author     Klaus Fiedler <klaus@tollwerk.de>
 * @copyright  Copyright © 2019 <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2019 Klaus Fiedler <klaus@tollwerk.de>
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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add new doktype as possible select item:
call_user_func(
    function ($extKey, $table) {
        $GLOBALS['TCA'][$table]['columns']['media']['config']['appearance']['fileUploadAllowed'] = false;
        $GLOBALS['TCA'][$table]['columns']['categories']['config']['foreign_table_where'] = 'AND sys_category.pid IN (###PAGE_TSCONFIG_IDLIST###) AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.sorting ASC';
        $GLOBALS['TCA'][$table]['columns']['starttime']['config']['eval'] = 'datetime,int';
        $GLOBALS['TCA'][$table]['columns']['title']['config']['eval'] = 'trim,required';
        $GLOBALS['TCA'][$table]['types'][\Tollwerk\TwBlog\Domain\Model\BlogArticle::DOKTYPE] = $GLOBALS['TCA'][$table]['types']['1'];

        // Add new columns
        $newColumns = [
            'tx_twblog_blog_teaser_text' => [
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_teaser_text',
                'config' => [
                    'type' => 'text',
                    'cols' => 24,
                    'rows' => 6,
                    'eval' => 'trim'
                ]
            ],
            'tx_twblog_blog_teaser_image' => [
                'exclude' => 0,
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_teaser_image',
                'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                    'tx_twblog_blog_teaser_image',
                    [
                        'appearance' => [
                            'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
                        ],
                        'overrideChildTca' => [
                            'types' => [
                                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                    'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
                                ],
                            ],
                        ],

                        'maxitems' => 1,
                        'minitems' => 0
                    ],
                    $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
                ),
            ],
            'tx_twblog_blog_authors' => [
                'exclude' => true,
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_authors',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'be_users',
                    'foreign_table_where' => 'AND be_users.username NOT LIKE "\_cli%" ORDER BY be_users.realName ASC',
                    'MM' => 'tx_twblog_blog_article_author_mm',
                    'size' => 3,
                    'minitems' => 0,
                    'enableMultiSelectFilterTextfield' => true,
                ],
            ],
            'tx_twblog_blog_related_articles' => [
                'exclude' => true,
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_related_articles',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table' => 'pages',
                    'foreign_table_where' => 'AND pages.doktype = ' . \Tollwerk\TwBlog\Domain\Model\BlogArticle::DOKTYPE . ' AND sys_language_uid IN (-1,0)',
                    'size' => 10,
                    'minitems' => 0,
                    'enableMultiSelectFilterTextfield' => true,
                ],
            ],

            'tx_twblog_blog_comments' => [
                'exclude' => true,
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_comments',
                'l10n_mode' => 'exclude',
                'config' => [
                    'type' => 'inline',
                    'foreign_table' => 'tx_twblog_domain_model_comment',
                    'foreign_field' => 'parent',
                    'foreign_table_field' => 'parent_table',
                    'maxitems' => 9999,
                    'appearance' => [
                        'collapseAll' => 1,
                        'expandSingle' => 1,
                    ],
                ],
            ],
            'tx_twblog_blog_series' => [
                'exclude' => true,
                'label' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:pages.tx_twblog_blog_series',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'foreign_table' => 'tx_twblog_domain_model_blogseries',
                    'foreign_table_where' => 'AND tx_twblog_domain_model_blogseries.pid IN (###PAGE_TSCONFIG_IDLIST###) ORDER BY tx_twblog_domain_model_blogseries.title ASC',
                    'items' => [
                        ['---', 0]
                    ],
                    'size' => 1,
                ],
            ],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $newColumns);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            $table,
            '--div--;LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:plugin.blog,
            tx_twblog_blog_teaser_text, tx_twblog_blog_teaser_image, tx_twblog_blog_related_articles, tx_twblog_blog_authors,tx_twblog_blog_series,
            --div--;LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:plugin.blog.comments, tx_twblog_blog_comments',
            \Tollwerk\TwBlog\Domain\Model\BlogArticle::DOKTYPE
        );

        // Add new page type as possible select item:
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
            $table,
            'doktype',
            [
                'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang_db.xlf:plugin.blog',
                \Tollwerk\TwBlog\Domain\Model\BlogArticle::DOKTYPE,
                'EXT:' . $extKey . '/Resources/Public/Icons/Extension/apps-pagetree-page-blogpage.svg'
            ],
            '1',
            'after'
        );

        // Add icon for new page type:
        \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
            $GLOBALS['TCA'][$table],
            [
                'ctrl' => [
                    'typeicon_classes' => [
                        \Tollwerk\TwBlog\Domain\Model\BlogArticle::DOKTYPE => 'apps-pagetree-blog',
                    ],
                ],
            ]
        );

        /*
        // Custom crop variants
        $GLOBALS['TCA'][$table]['columns']['tx_twblog_blog_teaser_image']['config']['overrideChildTca']['columns']['crop']['config'] = [
            'cropVariants' => [
                'blog_thumbnail'       => [
                    'title'               => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_thumbnail',
                    'allowedAspectRatios' => [
                        'default' => [
                            'title' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_thumbnail',
                            'value' => 1
                        ],
                    ],
                ],
                'blog_teaser'          => [
                    'title'               => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_teaser',
                    'allowedAspectRatios' => [
                        'default' => [
                            'title' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_teaser',
                            'value' => 1.944
                        ],
                    ],
                ],
                'blog_featured_teaser' => [
                    'title'               => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_featured_teaser',
                    'allowedAspectRatios' => [
                        'default' => [
                            'title' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_featured_teaser',
                            'value' => 2.36
                        ],
                    ],
                ],
                'blog_header'          => [
                    'title'               => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_header',
                    'allowedAspectRatios' => [
                        'default' => [
                            'title' => 'LLL:EXT:tw_blog/Resources/Private/Language/locallang.xlf:cropVariants.blog_header',
                            'value' => 0.0
                        ],
                    ],
                ],
            ],
        ];
        */
    },
    'tw_blog',
    'pages'
);
