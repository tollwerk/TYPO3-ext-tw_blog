<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain
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

namespace Tollwerk\TwBlog\Domain\Model;

use Tollwerk\TwBlog\Domain\Repository\BlogPostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Blog Series
 *
 * @package Tollwerk\TwBlog\Domain\Model
 */
class BlogSeries extends AbstractEntity
{
    /**
     * Series title
     *
     * @var string
     */
    protected $title = '';
    /**
     * Series description
     *
     * @var string
     */
    protected $description = '';
    /**
     * Blog posts in this series.
     *
     * @var QueryResultInterface
     */
    protected $posts = null;

    /**
     * Return the title
     *
     * @return string Title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title
     *
     * @param string $title Title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Return the description
     *
     * @return string Description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the description
     *
     * @param string $description Description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Return the posts of this series
     *
     * @return QueryResultInterface
     * @throws Exception
     */
    public function getPosts(): QueryResultInterface
    {
        if ($this->posts === null) {
            $this->posts = GeneralUtility::makeInstance(ObjectManager::class)
                                         ->get(BlogPostRepository::class)
                                         ->findBySeries($this);
        }

        return $this->posts;
    }
}
