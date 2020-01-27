<?php

/**
 * Blog
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Controller
 * @author     Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2019 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

declare(strict_types=1);

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

use Tollwerk\TwBlog\Domain\Model\BlogArticle;
use Tollwerk\TwBlog\Domain\Model\BlogAuthor;
use Tollwerk\TwBlog\Domain\Model\Category;
use Tollwerk\TwBlog\Domain\Model\Content;
use Tollwerk\TwBlog\Domain\Model\Comment;

return [
    Content::class     => [
        'tableName'  => 'tt_content',
        'properties' => [
            'type'     => [
                'fieldName' => 'CType'
            ],
            'image'    => [
                'fieldName' => 'image'
            ],
            'deleted'  => [
                'fieldName' => 'deleted'
            ],
            'flexform' => [
                'fieldName' => 'pi_flexform'
            ],
            'listType' => [
                'fieldName' => 'list_type'
            ],
        ],
    ],
    BlogArticle::class => [
        'tableName'  => 'pages',
        'properties' => [
            'created'            => [
                'fieldName' => 'crdate'
            ],
            'lastmod'            => [
                'fieldName' => 'tstamp'
            ],
            'localizationConfig' => [
                'fieldName' => 'l18n_cfg'
            ],
            'navTitle' =>           [
                'fieldName' => 'nav_title',
            ],
            'teaserText'         => [
                'fieldName' => 'tx_twblog_blog_teaser_text'
            ],
            'teaserImage'        => [
                'fieldName' => 'tx_twblog_blog_teaser_image'
            ],
            'authors'            => [
                'fieldName' => 'tx_twblog_blog_authors'
            ],
            'blogSeries'         => [
                'fieldName' => 'tx_twblog_blog_series'
            ],
            'comments'           => [
                'fieldName' => 'tx_twblog_blog_comments'
            ],
            'relatedArticles'    => [
                'fieldName' => 'tx_twblog_blog_related_articles'
            ],
        ],
    ],
    BlogAuthor::class  => [
        'tableName'  => 'be_users',
        'properties' => [
            'frontendImage' => [
                'fieldName' => 'tx_twblog_frontend_image'
            ],
            'frontendName'  => [
                'fieldName' => 'tx_twblog_frontend_name'
            ],
        ],
    ],
    Category::class    => [
        'tableName' => 'sys_category',
    ],
];
