<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Hooks
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

namespace Tollwerk\TwBlog\Hooks;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication as CoreBackendUserAuthentication;
use TYPO3\CMS\Core\Resource\Folder;

/**
 * Backend User Authentication Hook
 *
 * @package Tollwerk\TwBlog\Hooks
 */
class BackendUserAuthentication
{
    /**
     * Return a custom upload folder
     *
     * @param array $params
     * @param CoreBackendUserAuthentication $backendUserAuthentication
     *
     * @return mixed|Folder
     */
    public function getDefaultUploadFolder(Array $params, CoreBackendUserAuthentication $backendUserAuthentication)
    {
        // Change the upload folder for blog teaser images
        if (($params['table'] == 'pages') && ($params['field'] == 'tx_twblog_blog_teaser_image')) {
            $uploadFolder = new Folder($params['uploadFolder']->getStorage(), '/user_upload/blog/', 'blog');

            return $uploadFolder;
        }

        return $params['uploadFolder'];
    }
}