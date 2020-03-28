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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Blog Post
 *
 * @package Tollwerk\TwBlog\Domain\Model
 */
class BlogPost extends AbstractEntity
{
    /**
     * Blog document type
     *
     * @var int
     */
    const DOKTYPE = 116;

    /**
     * Blog post ID
     *
     * @var int
     */
    protected $uid;

    /**
     * Blog directory ID
     *
     * @var int
     */
    protected $pid;

    /**
     * Doktype
     *
     * @var int
     */
    protected $doktype;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * Blog title
     *
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * Teaser text
     *
     * @var string
     */
    protected $teaserText = '';

    /**
     * Teaser image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $teaserImage = null;

    /**
     * Media
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> media
     */
    protected $media = null;

    /**
     * Related posts
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\BlogPost> relatedPosts
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $relatedPosts = null;

    /**
     * Blog publishing date
     *
     * @var int
     */
    protected $starttime = 0;

    /**
     * Blog creation date
     *
     * @var int
     */
    protected $created = 0;

    /**
     * Blog last modification
     *
     * @var int
     */
    protected $tstamp = 0;
    /**
     * Localization configuration
     *
     * @var int
     */
    protected $localizationConfig = 0;

    /**
     * Authors
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\BlogAuthor>
     */
    protected $authors = null;

    /**
     * Comments
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\Comment>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $comments = null;

    /**
     * Blog Series
     *
     * @var \Tollwerk\TwBlog\Domain\Model\BlogSeries
     */
    protected $series = null;

    /**
     * Categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\Category>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $categories = null;

    /**
     * Disable comments
     *
     * @var bool
     */
    protected $disableComments = false;

    /**
     * Disable webmentions
     *
     * @var bool
     */
    protected $disableWebmentions = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->authors  = new ObjectStorage();
        $this->comments = new ObjectStorage();
        $this->media    = new ObjectStorage();
    }

    /**
     * Return the authors
     *
     * @return ObjectStorage Authors
     */
    public function getAuthors(): ObjectStorage
    {
        return $this->authors;
    }

    /**
     * Set authors
     *
     * @param ObjectStorage $authors Authors
     */
    public function setAuthors(ObjectStorage $authors): void
    {
        $this->authors = $authors;
    }

    /**
     * Return the unique identifier
     *
     * @return int Unique identifier
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * Set the unique identifier
     *
     * @param int $uid Unique identifier
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * Return the blog directory
     *
     * @return int Blog directory
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Return the doktype
     *
     * @return int Doktype
     */
    public function getDoktype(): int
    {
        return $this->doktype;
    }

    /**
     * Set the doktype
     *
     * @param int $doktype Doktype
     */
    public function setDoktype(int $doktype): void
    {
        $this->doktype = $doktype;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Return the blog title
     *
     * @return string Title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the blog title
     *
     * @param string $title Title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle(string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }


    /**
     * Return the teaser text
     *
     * @return string Teaser text
     */
    public function getTeaserText(): string
    {
        return $this->teaserText;
    }

    /**
     * Set the teaser text
     *
     * @param string $teaserText Teaser text
     */
    public function setTeaserText(string $teaserText): void
    {
        $this->teaserText = $teaserText;
    }

    /**
     * Return the start time
     *
     * @return int Start time
     */
    public function getStarttime(): int
    {
        return $this->starttime;
    }

    /**
     * Set the start time
     *
     * @param int $starttime Start time
     */
    public function setStarttime(int $starttime): void
    {
        $this->starttime = $starttime;
    }

    /**
     * Return the creation date
     *
     * @return int Creation date
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Set the creation date
     *
     * @param int $created Creation date
     */
    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    /**
     * Return the modification date
     *
     * @return int Creation date
     */
    public function getTstamp(): int
    {
        return $this->tstamp;
    }

    /**
     * Set the creation date
     *
     * @param int $tstamp modification date
     */
    public function setTstamp(int $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Return the teaser image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference Teaser image
     */
    public function getTeaserImage(): ?\TYPO3\CMS\Extbase\Domain\Model\FileReference
    {
        return $this->teaserImage;
    }

    /**
     * Set the teaser image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $teaserImage Teaser image
     */
    public function setTeaserImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $teaserImage)
    {
        $this->teaserImage = $teaserImage;
    }

    /**
     * Add a media file
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $media
     *
     * @return void
     */
    public function addMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $media): void
    {
        $this->media->attach($media);
    }

    /**
     * Remove a media file
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $mediaToRemove The media object to be removed
     *
     * @return void
     */
    public function removeMedia(\TYPO3\CMS\Extbase\Domain\Model\FileReference $mediaToRemove): void
    {
        $this->media->detach($mediaToRemove);
    }

    /**
     * Returns the media
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> media
     */
    public function getMedia(): \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->media;
    }

    /**
     * Sets the media
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     *
     * @return void
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media): void
    {
        $this->media = $media;
    }

    /**
     * Return the blog series
     *
     * @return \Tollwerk\TwBlog\Domain\Model\BlogSeries
     */
    public function getSeries(): ?BlogSeries
    {
        return $this->series;
    }

    /**
     * Set the blog series
     *
     * @param \Tollwerk\TwBlog\Domain\Model\BlogSeries $series
     */
    public function setSeries(BlogSeries $series): void
    {
        $this->series = $series;
    }

    /**
     * Add a comment
     *
     * @param \Tollwerk\TwBlog\Domain\Model\Comment $comments
     *
     * @return void
     */
    public function addComment(Comment $comments): void
    {
        $this->comments->attach($comments);
    }

    /**
     * Remove a comment
     *
     * @param \Tollwerk\TwBlog\Domain\Model\Comment $commentsToRemove The comment to be removed
     *
     * @return void
     */
    public function removeComment(Comment $commentsToRemove): void
    {
        $this->comments->detach($commentsToRemove);
    }

    /**
     * Return all comments
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\Comment> comments
     */
    public function getComments(): ObjectStorage
    {
        return $this->comments;
    }

    /**
     * Set the comments
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\Comment> $comments
     *
     * @return void
     */
    public function setComments(ObjectStorage $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * Return the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category>
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * Set the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     *
     * @return void
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * Return the localization configuration
     *
     * @return int
     */
    public function getLocalizationConfig(): int
    {
        return $this->localizationConfig;
    }

    /**
     * Set the localization configuration
     *
     * @param int $localizationConfig
     */
    public function setLocalizationConfig(int $localizationConfig): void
    {
        $this->localizationConfig = $localizationConfig;
    }


    /**
     * Add a relatedPost
     *
     * @param \Tollwerk\TwBlog\Domain\Model\BlogPost $relatedPost
     *
     * @return void
     */
    public function addRelatedPosts(BlogPost $relatedPost): void
    {
        $this->relatedPosts->attach($relatedPost);
    }

    /**
     * Remove a relatedPost
     *
     * @param \Tollwerk\TwBlog\Domain\Model\BlogPost $relatedPostToRemove The related post to be removed
     *
     * @return void
     */
    public function removeRelatedPost(BlogPost $relatedPostToRemove): void
    {
        $this->relatedPosts->detach($relatedPostToRemove);
    }

    /**
     * Return all relatedPosts
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\BlogPost> relatedPosts
     */
    public function getRelatedPosts(): ObjectStorage
    {
        return $this->relatedPosts;
    }

    /**
     * Set the relatedPosts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Tollwerk\TwBlog\Domain\Model\BlogPost> $relatedPosts
     *
     * @return void
     */
    public function setRelatedPosts(ObjectStorage $relatedPosts): void
    {
        $this->relatedPosts = $relatedPosts;
    }

    /**
     * Return the last modification date (as UNIX timestamp)
     *
     * @return int
     */
    public function getLastUpdated(): int
    {
        return max($this->getCreated(), $this->getTstamp(), $this->getStarttime());
    }

    /**
     * Return whether comments are disabled for this blog post
     *
     * @return bool Comments disabled
     */
    public function isDisableComments(): bool
    {
        return $this->disableComments;
    }

    /**
     * Set whether comments are disabled for this blog post
     *
     * @param bool $disableComments Comments disabled
     */
    public function setDisableComments(bool $disableComments): void
    {
        $this->disableComments = $disableComments;
    }

    /**
     * Return whether webmentions are disabled for this blog post
     *
     * @return bool Webmentions disabled
     */
    public function isDisableWebmentions(): bool
    {
        return $this->disableWebmentions;
    }

    /**
     * Set whether webmentions are disabled for this blog post
     *
     * @param bool $disableWebmentions Webmentions disabled
     */
    public function setDisableWebmentions(bool $disableWebmentions): void
    {
        $this->disableWebmentions = $disableWebmentions;
    }
}
