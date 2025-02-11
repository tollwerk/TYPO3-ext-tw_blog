<?php

/**
 * tollwerk
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Controller
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

namespace Tollwerk\TwBlog\Controller;

use Tollwerk\TwBlog\Domain\Model\Category;
use Tollwerk\TwBlog\Domain\Repository\BlogArticleRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Blog Controller
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Controller
 */
class BlogController extends ActionController
{
    /**
     * Featured blog article
     *
     * @var string
     */
    const DISPLAY_MODE_FEATURED = 'featured';
    /**
     * Standard blog article
     *
     * @var string
     */
    const DISPLAY_MODE_DEFAULT = 'default';
    /**
     * Select blog articles by storage PID
     *
     * @var int
     */
    const SELECTION_MODE_PAGES = 1;
    /**
     * Select blog articles manually
     *
     * @var int
     */
    const SELECTION_MODE_MANUAL = 2;

    /**
     * Blog article repository
     *
     * @var BlogArticleRepository
     */
    protected $blogArticleRepository;
    /**
     * The current blog article ID
     *
     * @var int
     */
    protected $uid;

    /**
     * Inject the blog article repository
     *
     * @param BlogArticleRepository $blogArticleRepository
     *
     * @return void
     */
    public function injectBlogArticleRepository(BlogArticleRepository $blogArticleRepository): void
    {
        $this->blogArticleRepository = $blogArticleRepository;
    }

    /**
     * Initializes the view before invoking an action method
     *
     * @param ViewInterface $view View
     */
    public function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
    }

    /**
     * List action
     *
     * @param int[]|null $categories Categories
     * @param int $offset            Pagination offset
     * @param int|null $orderBy      Article sorting
     * @param bool $showDisabled     Show disabled articles
     *
     * @throws InvalidQueryException
     */
    public function listAction(
        array $categories = null,
        $offset = 0,
        int $orderBy = null,
        bool $showDisabled = false
    ): void {

        // Show categories
        $showCategories = ($categories === null) ?
            GeneralUtility::trimExplode(',', $this->settings['categories'], true) : $categories;

        // Show featured articles?
        $showFeatured = ($this->settings['display_mode'] == self::DISPLAY_MODE_FEATURED);

        // Show disabled articles?
        $showDisabled = ($showDisabled === true) ? true : !empty($this->settings['show_disabled']);

        // Article ordering
        if ($orderBy === null) {
            $orderBy = isset($this->settings['order_by']) ? intval($this->settings['order_by']) : BlogArticleRepository::ORDER_BY_STARTTIME;
        }

        // Offset & pagination
        $offset = intval($showFeatured ? 0 : $offset);
        $articlesPerPage = intval($this->settings['articles_per_page'] ?? $this->settings['blog']['articlesPerPage']);

        // Select blog articles
        $countAll = null;
        if ($this->settings['selection_mode'] == self::SELECTION_MODE_MANUAL) {
            $blogArticles = $this->blogArticleRepository->findLimitedByUids(
                GeneralUtility::trimExplode(',', $this->settings['articles'], true),
                $offset,
                $articlesPerPage,
                $showCategories,
                $showDisabled,
                $countAll
            );
        } else {
            $blogArticles = $this->blogArticleRepository->findLimited(
                $offset,
                $articlesPerPage,
                $showCategories,
                $showDisabled,
                $orderBy,
                [],
                $countAll
            );
        }

        $this->view->assignMultiple([
            'articlesPerPage' => $articlesPerPage,
            'orderBy' => $orderBy,
            'showDisabled' => $showDisabled,
            'countAll' => $countAll,
            'blogArticles' => $blogArticles,
            'pagination' => $showFeatured ? null : static::pagination($offset, $articlesPerPage, $countAll),
            'offset' => $offset,
            'uid' => $this->uid,
            'categories' => $categories
        ]);
    }

    /**
     * Prepare paginator data
     *
     * @param int $currentOffset Current offset
     * @param int $itemsPerPage  Items per page
     * @param int $numberOfItems Number of items
     *
     * @return array Paginator data
     */
    public static function pagination(int $currentOffset, int $itemsPerPage, int $numberOfItems): array
    {
        // Get all pages of items for further calculation
        $pageIndex = 1;
        $allPages = [$pageIndex => 0];
        $offsetCounter = 0;
        $currentPageIndex = $pageIndex;
        while ($offsetCounter < $numberOfItems) {
            ++$offsetCounter;
            if (($offsetCounter % $itemsPerPage) == 0) {
                ++$pageIndex;
                $allPages[$pageIndex] = $offsetCounter;
            }
            if ($offsetCounter == $currentOffset) {
                $currentPageIndex = $pageIndex;
            }
        }

        return [
            'allPages' => $allPages,
            'currentPageIndex' => $currentPageIndex,
            'pages' => self::getPaginationPages($allPages, $currentPageIndex),
            'offsets' => self::getPaginationOffsets($currentOffset, $itemsPerPage, $allPages),
        ];
    }

    /**
     * Return the pagination pages
     *
     * @param array $allPages       All pages
     * @param int $currentPageIndex Current page index
     *
     * @return array Pagination pages
     */
    protected static function getPaginationPages(array $allPages, int $currentPageIndex): array
    {
        // Prepare $pages array
        $showPrevNext = 1;
        $showFirstLast = 2;
        $pages = [
            'first' => [],
            'prev' => [],
            'current' => [$currentPageIndex => $allPages[$currentPageIndex]],
            'next' => [],
            'last' => [],
        ];

        // Get current->previous pages
        for ($i = $currentPageIndex - 1; $i > ($currentPageIndex - 1 - $showPrevNext); --$i) {
            if (($i > 0) && ($i < $currentPageIndex)) {
                $pages['prev'][$i] = $allPages[$i];
            }
        }

        // Get first pages (only if not already included in previous pages)
        for ($i = 0; $i < $showFirstLast; ++$i) {
            $index = $i + 1;
            if (($index > 0) && ($index < $currentPageIndex) && !array_key_exists($index, $pages['prev'])) {
                $pages['first'][$index] = $allPages[$index];
            }
        }

        // Get current->next pages
        for ($i = $currentPageIndex + 1; $i < ($currentPageIndex + 1 + $showPrevNext); $i++) {
            if (($i > $currentPageIndex) && ($i <= count($allPages))) {
                $pages['next'][$i] = $allPages[$i];
            }
        }

        // Get last pages
        for ($i = count($allPages); $i > (count($allPages) - $showFirstLast); $i--) {
            if (($i > $currentPageIndex) && ($i <= count($allPages)) && !array_key_exists($i, $pages['next'])) {
                $pages['last'][$i] = $allPages[$i];
            }
        }

        return array_filter($pages);
    }

    /**
     * Return the pagination offsets
     *
     * @param int $currentOffset Current offset
     * @param int $itemsPerPage  Items per page
     * @param array $allPages    All pages
     *
     * @return array Pagination offsets
     */
    protected static function getPaginationOffsets(int $currentOffset, int $itemsPerPage, array $allPages): array
    {
        return [
            'first' => 0,
            'prev' => $currentOffset - $itemsPerPage,
            'current' => $currentOffset,
            'next' => $currentOffset + $itemsPerPage,
            'last' => $allPages[count($allPages)]
        ];
    }

    /**
     * Blog category filter action
     *
     * @param int[] $categories Categories
     */
    public function filterAction(array $categories = [])
    {
        $rootCategory = intval($this->settings['blog']['rootCategory']);
        $allCategories = [];
        /** @var Category $category */
        foreach ($this->objectManager->get(CategoryRepository::class)->findByParent($rootCategory) as $category) {
            if (in_array($category->getUid(), $categories)) {
                $category->setActive(true);
            }
            $allCategories[] = $category;
        }
        $this->view->assign('categories', $allCategories);
    }

    /**
     * Navigate action
     *
     * @param int[] $categories Categories
     *
     * @throws InvalidConfigurationTypeException
     */
    public function navigationAction(array $categories = [])
    {
        $current = $this->blogArticleRepository->findByIdentifier($GLOBALS['TSFE']->id);
        $this->view->assign('current', $current);
        $this->view->assign('previous', $this->blogArticleRepository->findPrevious($current, $categories));
        $this->view->assign('next', $this->blogArticleRepository->findNext($current, $categories));
        $this->view->assign('categories', $categories);
    }

    /**
     * Controler initialization
     */
    protected function initializeAction(): void
    {
        parent::initializeAction();
        $this->uid = $this->configurationManager->getContentObject()->data['uid'];
    }
}
