<?php

/**
 * data
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\ViewHelpers
 * @author     Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2018 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2018 tollwerk GmbH <info@tollwerk.de>
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

namespace Tollwerk\TwBlog\ViewHelpers\BlogArticle;

use Tollwerk\TwBlog\Domain\Repository\BlogArticleRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Select a layout by document type
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\ViewHelpers\Page
 */
class PreviousViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('blogArticle', '\Tollwerk\TwBlog\Domain\Model\BlogArticle', 'The current blog article',
            true);
    }

    /**
     * Select a layout by document type
     *
     * @return array|null
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @api
     */
    public function render()
    {
        $objectManager         = GeneralUtility::makeInstance(ObjectManager::class);
        $blogArticleRepository = $objectManager->get(BlogArticleRepository::class);

        return $blogArticleRepository->findPrevious($this->arguments['blogArticle']);
    }
}
