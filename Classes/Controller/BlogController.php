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

use Tollwerk\TwBlog\Domain\Model\BlogPost;
use Tollwerk\TwBlog\Domain\Model\Category;
use Tollwerk\TwBlog\Domain\Repository\BlogPostRepository;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Blog Controller
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Controller
 */
class BlogController extends ActionController
{
    /**
     * Featured blog post
     *
     * @var string
     */
    const DISPLAY_MODE_FEATURED = 'featured';
    /**
     * Standard blog post
     *
     * @var string
     */
    const DISPLAY_MODE_DEFAULT = 'default';
    /**
     * Select blog posts by storage PID
     *
     * @var int
     */
    const SELECTION_MODE_PAGES = 1;
    /**
     * Select blog posts manually
     *
     * @var int
     */
    const SELECTION_MODE_MANUAL = 2;

    /**
     * Blog post repository
     *
     * @var BlogPostRepository
     */
    protected $blogPostRepository;
    /**
     * The current blog post ID
     *
     * @var int
     */
    protected $uid;

    /**
     * Inject the blog post repository
     *
     * @param BlogPostRepository $blogPostRepository
     *
     * @return void
     */
    public function injectBlogPostRepository(BlogPostRepository $blogPostRepository): void
    {
        $this->blogPostRepository = $blogPostRepository;
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
     * @param int|null $orderBy      Post sorting
     * @param bool $showDisabled     Show disabled posts
     *
     * @throws InvalidQueryException
     */
    public function listAction(
        array $categories = null,
        int $offset = 0,
        int $orderBy = null,
        bool $showDisabled = false
    ): void {
        // Show categories
        $showCategories = ($categories === null) ?
            GeneralUtility::trimExplode(',', $this->settings['categories'], true) : $categories;

        // Show featured posts?
        $showFeatured = ($this->settings['display_mode'] == self::DISPLAY_MODE_FEATURED);

        // Show disabled posts?
        $showDisabled = ($showDisabled === true) ? true : !empty($this->settings['show_disabled']);

        // Post ordering
        if ($orderBy === null) {
            $orderBy = isset($this->settings['order_by']) ? intval($this->settings['order_by']) : BlogPostRepository::ORDER_BY_STARTTIME;
        }

        // Offset & pagination
        $offset       = intval($showFeatured ? 0 : $offset);
        $postsPerPage = intval($this->settings['posts_per_page'] ?? $this->settings['blog']['postsPerPage']);

        // Select blog posts
        $countAll = null;
        if ($this->settings['selection_mode'] == self::SELECTION_MODE_MANUAL) {
            $blogPosts = $this->blogPostRepository->findLimitedByUids(
                GeneralUtility::trimExplode(',', $this->settings['posts'], true),
                $offset,
                $postsPerPage,
                $showCategories,
                $showDisabled,
                $countAll
            );
        } else {
            $blogPosts = $this->blogPostRepository->findLimited(
                $offset,
                $postsPerPage,
                $showCategories,
                $showDisabled,
                $orderBy,
                [],
                $countAll
            );
        }

        $this->view->assignMultiple([
            'postsPerPage' => $postsPerPage,
            'orderBy'      => $orderBy,
            'showDisabled' => $showDisabled,
            'blogPosts'    => $blogPosts,
            'pagination'   => $showFeatured ? null : static::pagination($offset, $postsPerPage, $countAll),
            'uid'          => $this->uid,
            'categories'   => $categories,
            'offset'       => $offset,
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
        $currentPage  = floor($currentOffset / $itemsPerPage) + 1;
        $pages        = array_map(
            function($page) use ($itemsPerPage) {
                return $page * $itemsPerPage;
            },
            array_flip(array_map('intval', range(1, floor($numberOfItems / $itemsPerPage) + 1)))
        );
        $destinations = [
            'first'    => 1,
            'previous' => $currentPage - 1,
            'current'  => $currentPage,
            'next'     => ($currentPage < count($pages)) ? ($currentPage + 1) : 0,
            'last'     => count($pages)
        ];

        return [
            'pages'        => $pages,
            'destinations' => $destinations,
            'range'        => [
                'from' => $currentOffset + 1,
                'to'   => min($currentOffset + $itemsPerPage, $numberOfItems),
            ],
            'total'        => $numberOfItems
        ];
    }

    /**
     * Blog category filter action
     *
     * @param int[] $categories Categories
     */
    public function filterAction(array $categories = [])
    {
        $rootCategory  = intval($this->settings['blog']['rootCategory']);
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
     * @param int $offset       Pagination offset
     *
     * @throws InvalidConfigurationTypeException
     */
    public function navigationAction(array $categories = [], int $offset = 0)
    {
        $current       = $this->blogPostRepository->findByIdentifier($GLOBALS['TSFE']->id);
        $postsPerPage  = intval($this->settings['blog']['postsPerPage']);
        $currentOffset = $this->blogPostRepository->findCurrentOffset($current, $categories, $postsPerPage);
        $this->view->assign('current', $current);
        $this->view->assign('currentOffset', $currentOffset);
        $this->view->assign('previous', $this->blogPostRepository->findPrevious($current, $categories));
        $this->view->assign('next', $this->blogPostRepository->findNext($current, $categories));
        $this->view->assign('categories', $categories);
        $this->view->assign('offset', $offset);
    }

    /**
     * Use the XML format for the atom action
     */
    public function initializeAtomAction()
    {
        $this->request->setFormat('xml');
    }

    /**
     * Atom feed
     */
    public function atomAction()
    {
        $blogPosts = $this->blogPostRepository->findLimited(
            0,
            0,
            [],
            false,
            BlogPostRepository::ORDER_BY_STARTTIME,
            [$this->settings['blog']['storagePid']]
        );

        $lastUpdated = max(array_map(function(BlogPost $post) {
            return $post->getLastUpdated();
        }, $blogPosts->toArray()));

        $this->view->assignMultiple([
            'blogPosts'   => $blogPosts,
            'lastUpdated' => new \DateTimeImmutable('@'.$lastUpdated),
            'icon'        => $this->graphicResourceUrl($this->settings['atom']['icon']),
            'logo'        => $this->graphicResourceUrl($this->settings['atom']['logo']),
        ]);

        $this->response->setHeader('Content-Type', 'application/atom+xml; charset=UTF-8');
        $this->response->setContent($this->view->render());
        $this->response->send();
        $this->response->shutdown();
        exit;
    }

    /**
     * Return a root relative graphic URL
     *
     * @param string $graphic Graphic resource
     *
     * @return string|null Relative graphic URL
     */
    protected function graphicResourceUrl(string $graphic): ?string
    {
        $graphic = trim($graphic);
        if (strlen($graphic)) {
            $absGraphic = GeneralUtility::getFileAbsFileName($graphic);
            $sitePath   = Environment::getPublicPath();
            if (strlen($absGraphic) && !strncmp($sitePath, $absGraphic, strlen($sitePath))) {
                return substr($absGraphic, strlen($sitePath));
            }
        }

        return null;
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
