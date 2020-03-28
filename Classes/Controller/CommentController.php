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
use Tollwerk\TwBlog\Domain\Model\Comment;
use Tollwerk\TwBlog\Domain\Repository\BlogPostRepository;
use Tollwerk\TwBlog\Domain\Repository\CommentRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Service\CacheService;

/**
 * Comment Controller
 *
 * @package Tollwerk\TwBlog\Controller
 */
class CommentController extends ActionController
{
    /**
     * Comment repository
     *
     * @var CommentRepository
     */
    protected $commentRepository;
    /**
     * Blog Post repository
     *
     * @var BlogPostRepository
     */
    protected $blogPostRepository;

    /**
     * Inject the comment repository
     *
     * @param CommentRepository $commentRepository
     *
     * @return void
     */
    public function injectCommentRepository(CommentRepository $commentRepository): void
    {
        $this->commentRepository = $commentRepository;
    }

    /**
     * Inject the blog post repository
     *
     * @param BlogPostRepository $blogPostRepository Blog Post repository
     */
    public function injectBlogPostRepository(BlogPostRepository $blogPostRepository): void
    {
        $this->blogPostRepository = $blogPostRepository;
    }

    /**
     * Render a comment form
     */
    public function formAction(): void
    {
        // Data from $cObj->data are available if plugin gets called via fluid template with <f:cObject typoscriptObjectPath data="..."/>
        $cObj = $this->configurationManager->getContentObject();
        $this->view->assignMultiple([
            'pid'         => isset($cObj->data['pid']) ? $cObj->data['pid'] : $this->settings['pid'],
            'parent'      => isset($cObj->data['parent']) ? $cObj->data['parent'] : $this->settings['parent'],
            'parentTable' => isset($cObj->data['parentTable']) ? $cObj->data['parentTable'] : $this->settings['parentTable'],
        ]);
    }

    /**
     * Create a comment
     *
     * @param Comment $newComment
     * @param int $parent
     * @param string $parentTable
     * @param int $pid
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Comment $newComment, $parent, $parentTable, $pid)
    {
        $newComment->setParent($parent);
        $newComment->setParentTable($parentTable);
        $newComment->setPid($pid);

        $this->commentRepository->add($newComment);
        $this->objectManager->get(PersistenceManager::class)->persistAll();
        $this->redirect('form');
    }

    /**
     * Confirm a comment
     *
     * @param string $confirmation Confirmed comment
     *
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function confirmAction(string $confirmation)
    {
        $comment = $this->commentRepository->findOneByConfirmation($confirmation);
        if ($comment instanceof Comment) {
            $blogPost = $this->blogPostRepository->findByUid($comment->getParent());
            if ($blogPost instanceof BlogPost) {
                // Confirm the comment
                $comment->setHidden(false);
                $comment->setConfirmation('');
                $this->commentRepository->update($comment);

                // Clear the blog post page cache
                $this->objectManager->get(CacheService::class)->clearPageCache($blogPost->getUid());

                // Call initialization hooks
                $params = ['comment' => $comment, 'blogPost' => $blogPost];
                foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['blogComment']['confirmation'] as $confirmationHook) {
                    GeneralUtility::callUserFunction($confirmationHook, $params, $this);
                }

                // Variable assignment
                $this->view->assignMultiple([
                    'comment'  => $comment,
                    'blogPost' => $this->blogPostRepository->findByUid($comment->getParent())
                ]);
            }
        }
    }
}
