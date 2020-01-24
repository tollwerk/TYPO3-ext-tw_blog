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

use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;

/**
 * Blog Author
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain\Model
 */
class BlogAuthor extends BackendUser
{
    /**
     * Frontend name
     *
     * @var string
     */
    protected $frontendName = '';

    /**
     * Frontend image
     *
     * @var FileReference
     */
    protected $frontendImage = null;

    /**
     * Avatar image
     *
     * @var FileReference
     */
    protected $avatar = null;

    /**
     * Return the frontend name
     *
     * @return string
     */
    public function getFrontendName(): string
    {
        return $this->frontendName;
    }

    /**
     * Set the frontend name
     *
     * @param string $frontendName
     */
    public function setFrontendName(string $frontendName): void
    {
        $this->frontendName = $frontendName;
    }

    /**
     * Get the avatar
     *
     * @return FileReference
     */
    public function getFrontendImage(): ?FileReference
    {
        return $this->frontendImage;
    }

    /**
     * Set the avatar
     *
     * @param FileReference $frontendImage
     */
    public function setFrontendImage(FileReference $frontendImage): void
    {
        $this->frontendImage = $frontendImage;
    }

    /**
     * Return the avatar image
     *
     * @return FileReference
     */
    public function getAvatar(): ?FileReference
    {
        return $this->avatar;
    }

    /**
     * Set the avatar image
     *
     * @param FileReference $avatar
     */
    public function setAvatar(FileReference $avatar): void
    {
        $this->avatar = $avatar;
    }
}
