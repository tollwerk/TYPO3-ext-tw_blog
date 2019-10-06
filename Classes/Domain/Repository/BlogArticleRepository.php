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

namespace Tollwerk\TwBlog\Domain\Repository;

use Tollwerk\TwBlog\Domain\Model\BlogArticle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Blog Article Repository
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain\Repository
 */
class BlogArticleRepository extends AbstractRepository
{
    const DOKTYPE = 116;
    const ORDER_BY_STARTTIME = 1;
    const ORDER_BY_SORTING = 2;

    /**
     * Blog article storage PIDs
     *
     * @var int[]
     */
    protected $storagePids = null;

    /**
     * Default ordering
     *
     * @var array
     */
    protected $defaultOrderings = array(
        'starttime' => QueryInterface::ORDER_DESCENDING,
        'uid'       => QueryInterface::ORDER_DESCENDING
    );

    /**
     * Returns all blog posts
     *
     * @param array $storagePids Optional: Storage PIDs
     * @param bool $showDisabled
     *
     * @return QueryResultInterface|array
     */
    public function findAll(array $storagePids = [], bool $showDisabled = false)
    {
        $query = $this->createQuery();
        if (!empty($storagePids)) {
            $query->getQuerySettings()->setStoragePageIds($storagePids);
        }
        $constraints = $this->getDefaultConstraints($query);
        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    /**
     * Return array with default constraints that should be used for all queries
     * like only finding pages with the right doktype (116)
     *
     * @param QueryInterface $query
     *
     * @return array Constraints
     */
    protected function getDefaultConstraints(&$query): array
    {
        return [
            $query->equals('doktype', self::DOKTYPE)
        ];
    }

    /**
     * Finds an blog article matching the given identifier even if it's deleted
     *
     * @param mixed $identifier The identifier of the object to find
     *
     * @return BlogArticle Blog article
     * @api
     */
    public function findOneByIdentifierDeleted($identifier): ?BlogArticle
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $constraints   = $this->getDefaultConstraints($query);
        $constraints[] = $query->equals('uid', $identifier);
        $query->matching($query->logicalAnd($constraints));

        return $query->execute()->getFirst();
    }

    /**
     * Find a limited number of blog articles by their uids
     *
     * @param array $uids        Article IDs
     * @param int $offset        Pagination offset
     * @param int $limit         Pagination limit
     * @param bool $showDisabled Include disabled articles
     * @param int $count         Overall article count (set by reference)
     *
     * @return BlogArticle[]
     * @throws InvalidQueryException
     */
    public function findLimitedByUids(
        array $uids = [],
        int $offset = 0,
        int $limit = 1,
        bool $showDisabled = false,
        int &$count = null
    ): array {
        // If no IDs were given: return
        if (!is_array($uids) || !count($uids)) {
            $count = 0;

            return [];
        }

        $query         = $this->createQuery();
        $constraints   = $this->getDefaultConstraints($query);
        $constraints[] = $query->in('uid', $uids);
        $query->matching($query->logicalAnd($constraints));

        // Include disabled articles?
        if ($showDisabled) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        // Run a count query without limits
        $countQuery = clone $query;
        $count      = $countQuery->execute()->count();

        // Offset & limit
        $query->setOffset($offset)->setLimit($limit);

        $blogArticles = array_fill_keys($uids, null);
        /** @var BlogArticle $blogArticle */
        foreach ($query->execute() as $blogArticle) {
            $blogArticles[$blogArticle->getUid()] = $blogArticle;
        }

        return array_values(array_filter($blogArticles));
    }

    /**
     * Find a limited number of blog articles
     *
     * @param int $offset        Pagination offset
     * @param int $limit         Pagination limit
     * @param bool $showDisabled Include disabled articles
     * @param int $orderBy       Ordering
     * @param array $storagePids Storage PIDS
     * @param int $count         Overall article count (set by reference)
     *
     * @return array|QueryResultInterface Blog articles
     */
    public function findLimited(
        int $offset = 0,
        int $limit = 1,
        bool $showDisabled = false,
        int $orderBy = self::ORDER_BY_STARTTIME,
        array $storagePids = [],
        int &$count = null
    ): ?QueryResultInterface {
        $query = $this->createQuery();
        $query->matching($query->logicalAnd($this->getDefaultConstraints($query)));

        // Include disabled articles?
        if ($showDisabled) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        // Use particular storage pages?
        if (count($storagePids)) {
            $query->getQuerySettings()->setStoragePageIds($storagePids);
        }

        // Run a count query without limits
        $countQuery = clone $query;
        $count      = $countQuery->execute()->count();

        // Order articles
        if ($orderBy == self::ORDER_BY_SORTING) {
            $query->setOrderings([
                'sorting' => QueryInterface::ORDER_ASCENDING,
            ]);
        } else {
            $query->setOrderings([
                'starttime' => QueryInterface::ORDER_DESCENDING,
                'uid'       => QueryInterface::ORDER_DESCENDING,
            ]);
        }

        // Offset & limit
        $query->setOffset($offset)->setLimit($limit);

        return $query->execute();
    }

    /**
     * Get next blog article by starttime or uid
     *
     * @param BlogArticle $blogArticle
     *
     * @return null|BlogArticle Next blog article
     * @throws InvalidConfigurationTypeException
     * @throws InvalidQueryException
     */
    public function findNext(BlogArticle $blogArticle): ?BlogArticle
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds($this->getStoragePids(['blog', 'storagePid']));
        $query->setLimit(1);
        $constraints = $this->getDefaultConstraints($query);

        if ($blogArticle->getStarttime()) {
            $constraints[] = $query->greaterThan('starttime', $blogArticle->getStarttime());
            $query->setOrderings([
                'starttime' => QueryInterface::ORDER_ASCENDING,
                'uid'       => QueryInterface::ORDER_ASCENDING
            ]);
        } else {
            $constraints[] = $query->greaterThan('uid', $blogArticle->getUid());
            $query->setOrderings(['uid' => QueryInterface::ORDER_ASCENDING]);
        }

        return $query->matching($query->logicalAnd($constraints))->execute()->current() ?: null;
    }

    /**
     * Get previous blog article by starttime or uid
     *
     * @param BlogArticle $blogArticle
     *
     * @return null|BlogArticle Previous Blog article
     * @throws InvalidConfigurationTypeException
     * @throws InvalidQueryException
     */
    public function findPrevious(BlogArticle $blogArticle): ?BlogArticle
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds($this->getStoragePids(['blog', 'storagePid']));
        $query->setLimit(1);
        $constraints = $this->getDefaultConstraints($query);

        if ($blogArticle->getStarttime()) {
            $constraints[] = $query->lessThan('starttime', $blogArticle->getStarttime());
        } else {
            $constraints[] = $query->lessThan('uid', $blogArticle->getUid());
            $query->setOrderings(['uid' => QueryInterface::ORDER_DESCENDING]);
        }

        return $query->matching($query->logicalAnd($constraints))->execute()->current() ?: null;
    }

    /**
     * Count all available blog posts
     *
     * @param bool $showDisabled
     *
     * @return int
     */
    public function countAll(bool $showDisabled = false): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $query        = $this->createQuery();
        if ($showDisabled) {
            $query->getQuerySettings()->setEnableFieldsToBeIgnored(['hidden']);
        }
        $statement = $queryBuilder
            ->select('uid')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('doktype', self::DOKTYPE),
                $queryBuilder->expr()->in('pid', $query->getQuerySettings()->getStoragePageIds())
            )
            ->execute();

        return $statement->rowCount();
    }

    /**
     * Recursively determine storage PIDs
     *
     * @param int $storagePid Root PID
     * @param int $recursive  Recursion levels
     *
     * @return array
     * @throws Exception
     */
    protected function getStoragePidsRecursive($storagePid = 1, $recursive = 99): array
    {
        $objectManager  = GeneralUtility::makeInstance(ObjectManager::class);
        $queryGenerator = $objectManager->get(QueryGenerator::class);

        return GeneralUtility::trimExplode(',', $queryGenerator->getTreeList($storagePid, $recursive));
    }
}
