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

/**
 * Comment
 *
 * @package Tollwerk\TwBlog\Domain\Model
 */
class  Comment extends AbstractEntity
{
    /**
     * Hidden
     *
     * @var bool
     */
    protected $hidden;
    /**
     * Privacy policy confirmation date
     *
     * @var int
     */
    protected $privacy;
    /**
     * Creation date
     *
     * @var int
     */
    protected $crdate;

    /**
     * Parent unique identifier
     *
     * @var int
     */
    protected $parent = 0;

    /**
     * Parent table
     *
     * @var string
     */
    protected $parentTable = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var string
     */
    protected $replies = '';
    /**
     * Confirmation hash
     *
     * @var string
     */
    protected $confirmation = '';
    /**
     * IP address
     *
     * @var string
     */
    protected $ip = '';

    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param int $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getParentTable()
    {
        return $this->parentTable;
    }

    /**
     * @param string $parentTable
     */
    public function setParentTable($parentTable)
    {
        $this->parentTable = $parentTable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getReplies()
    {
        return $this->replies;
    }

    /**
     * @param string $replies
     */
    public function setReplies($replies)
    {
        $this->replies = $replies;
    }

    /**
     * Return the creation date
     *
     * @return int Creation date
     */
    public function getCrdate(): ?int
    {
        return $this->crdate;
    }

    /**
     * Set the creation date
     *
     * @param int $crdate Creation date
     */
    public function setCrdate(int $crdate): void
    {
        $this->crdate = $crdate;
    }

    /**
     * Return the privacy policy confirmation date
     *
     * @return int Privacy policy confirmation date
     */
    public function getPrivacy(): ?int
    {
        return $this->privacy;
    }

    /**
     * Set the privacy policy confirmation date
     *
     * @param int $privacy Privacy policy confirmation date
     */
    public function setPrivacy(int $privacy): void
    {
        $this->privacy = $privacy;
    }

    /**
     * Return the confirmation hash
     *
     * @return string Confirmation hash
     */
    public function getConfirmation(): string
    {
        return $this->confirmation;
    }

    /**
     * Set the confirmation hash
     *
     * @param string $confirmation Confirmation hash
     */
    public function setConfirmation(string $confirmation): void
    {
        $this->confirmation = $confirmation;
    }

    /**
     * Return whether the comment is hidden
     *
     * @return bool Hidden
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set whether the comment is hidden
     *
     * @param bool $hidden Hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Return the IP address
     *
     * @return string IP address
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * Set the IP address
     *
     * @param string $ip IP address
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }
}
