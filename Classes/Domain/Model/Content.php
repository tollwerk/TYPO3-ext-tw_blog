<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain\Model
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
 * Content Element
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain\Model
 */
class Content extends AbstractEntity
{
    /**
     * Image orientation
     *
     * @var int
     */
    protected $imageorient;
    /**
     * CType
     *
     * @var string
     */
    protected $type;
    /**
     * Images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> media
     */
    protected $image;
    /**
     * Deleted
     *
     * @var bool
     */
    protected $deleted;
    /**
     * Flexform
     *
     * @var string
     */
    protected $flexform;
    /**
     * Bodytext
     *
     * @var string
     */
    protected $bodytext;
    /**
     * Header
     *
     * @var string
     */
    protected $header;
    /**
     * List Type
     *
     * @var string
     */
    protected $listType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->image = new ObjectStorage();
    }

    /**
     * Return the CType
     *
     * @return string CType
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the CType
     *
     * @param string $type CType
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Return the images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage Images
     */
    public function getImage(): ObjectStorage
    {
        return $this->image;
    }

    /**
     * Set the images
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $image Images
     */
    public function setImage(ObjectStorage $image): void
    {
        $this->image = $image;
    }

    /**
     * Return whether this element is deleted
     *
     * @return bool Deleted
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Set whether this element is deleted
     *
     * @param bool $deleted Deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * Return the flexform
     *
     * @return string Flexform
     */
    public function getFlexform(): string
    {
        return $this->flexform;
    }

    /**
     * Set the flexform
     *
     * @param string $flexform Flexform
     */
    public function setFlexform(string $flexform): void
    {
        $this->flexform = $flexform;
    }

    /**
     * Return the image orientation
     *
     * @return int Image orientation
     */
    public function getImageorient(): int
    {
        return $this->imageorient;
    }

    /**
     * Set the image oriantation
     *
     * @param int $imageorient Image orientation
     */
    public function setImageorient(int $imageorient): void
    {
        $this->imageorient = $imageorient;
    }

    /**
     * Return the bodytext
     *
     * @return string Bodytext
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * Set the bodytext
     *
     * @param string $bodytext Bodytext
     */
    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Return the header
     *
     * @return string Header
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * Set the header
     *
     * @param string $header Header
     */
    public function setHeader(string $header): void
    {
        $this->header = $header;
    }

    /**
     * Return the list type
     *
     * @return string List Type
     */
    public function getListType(): string
    {
        return $this->listType;
    }

    /**
     * Set the list type
     *
     * @param string $listType List Type
     */
    public function setListType(string $listType): void
    {
        $this->listType = $listType;
    }
}
