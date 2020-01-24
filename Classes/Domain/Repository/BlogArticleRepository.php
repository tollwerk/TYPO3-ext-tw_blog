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

use Doctrine\DBAL\FetchMode;
use Tollwerk\TwBlog\Domain\Model\BlogArticle;
use Tollwerk\TwBlog\Hooks\TwBlog\CreateQueryStatementInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
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
        'uid' => QueryInterface::ORDER_DESCENDING
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

        $constraints = $this->getDefaultConstraints($query);
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
     * @param array $categories  Categories
     * @param bool $showDisabled Include disabled articles
     * @param int $count         Overall article count (set by reference)
     *
     * @return QueryResultInterface Blog articles
     * @throws InvalidQueryException
     */
    public function findLimitedByUids(
        array $uids = [],
        int $offset = 0,
        int $limit = 1,
        array $categories = [],
        bool $showDisabled = false,
        int &$count = null
    ): ?QueryResultInterface {
        $count = 0;
        if (empty($uids)) {
            return null;
        }

        $query = $this->createQuery();
        $query->statement($this->createQueryStatement(
            $uids,
            $offset,
            $limit,
            $categories,
            $showDisabled,
            $query->getQuerySettings()->getStoragePageIds(),
            [],
            $count
        )->getSQL());

        return $query->execute();
    }

    /**
     * Create a query statement
     *
     * @param int[] $ids          Article IDs
     * @param int $offset         Pagination offset
     * @param int $limit          Pagination limit
     * @param int[] $categories   Categories
     * @param bool $showDisabled  Include disabled articles
     * @param string[] $orderings Ordering
     * @param int[] $storagePids  Storage PIDs
     * @param int $count          Overall article count (set by reference)
     *
     * @return QueryBuilder Query
     */
    protected function createQueryStatement(
        array $ids,
        int $offset,
        int $limit,
        array $categories,
        bool $showDisabled,
        array $storagePids,
        array $orderings,
        int &$count = null
    ): QueryBuilder {
        // Query the blog article IDs first
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('pages');
        $query = $connection->createQueryBuilder();
        if ($showDisabled) {
            $query->getRestrictions()->removeByType(HiddenRestriction::class);
            $query->getRestrictions()->removeByType(StartTimeRestriction::class);
            $query->getRestrictions()->removeByType(EndTimeRestriction::class);
        }

        // Call hook for manipulating the central query builder
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/tw_blog']['createQueryStatement'] ?? [] as $className) {
            /** @var CreateQueryStatementInterface $_procObj */
            $_procObj = GeneralUtility::makeInstance($className);
            if (!($_procObj instanceof CreateQueryStatementInterface)) {
                throw new \TYPO3\CMS\Extbase\Exception(
                    sprintf('The registered class %s for hook [ext/tw_blog][createQueryStatement] does not implement CreateQueryStatementInterface', $className),
                    1579850816
                );
            }
            $_procObj->createQueryStatement($query);
        }

        $query->select('p.*')
            ->from('pages', 'p')
            ->where($query->expr()->eq('p.doktype', static::DOKTYPE));

        // Localization handling
        $context = GeneralUtility::makeInstance(Context::class);
        $query->andWhere($query->expr()->eq('p.sys_language_uid', $context->getPropertyFromAspect('language', 'id')));

        // IDs
        if (count($ids)) {
            $query->andWhere($query->expr()->in('p.uid', $ids));

            // Order CASE statement
            $cases = array_map(function(int $index, int $id) {
                return " WHEN $id THEN $index";
            }, array_keys($ids), $ids);
            $query->addSelectLiteral(
                'CASE '.$query->quoteIdentifier('p.uid').implode('', $cases).
                ' END AS '.$query->quoteIdentifier('_ordervalues')
            );
            $orderings = ['_ordervalues' => QueryInterface::ORDER_ASCENDING];
        }

        // Storage pages
        if (count($storagePids)) {
            $query->andWhere($query->expr()->in('p.pid', $storagePids));
        }

        // Categories
        if (count($categories)) {
            $query->join(
                'p',
                'sys_category_record_mm',
                's',
                $query->expr()->andX(
                    $query->expr()->eq('s.tablenames', $query->quote('pages')),
                    $query->expr()->eq('s.fieldname', $query->quote('categories')),
                    $query->expr()->in('s.uid_local', $categories),
                    $query->expr()->in('s.uid_foreign', $query->quoteIdentifier('p.uid'))
                )
            );
            $count = $this->countArticles($query);
            $query->groupBy('p.uid');
        } else {
            $count = $this->countArticles($query);
        }

        // Offset & limit
        if (intval($limit)) {
            $query->setFirstResult($offset * $limit)->setMaxResults($limit);
        }

        // Ordering
        foreach ($orderings as $column => $direction) {
            $query->addOrderBy(strncmp($column, '_', 1) ? 'p.'.$column : $column, $direction);
        }

        return $query;
    }

    /**
     * Run an article count query
     *
     * @param QueryBuilder $query Query
     *
     * @return int Number of results
     */
    protected function countArticles(QueryBuilder $query): int
    {
        $countQuery = clone $query;
        $countQuery->selectLiteral('COUNT(DISTINCT '.$countQuery->quoteIdentifier('p.uid').')');

        return intval($countQuery->execute()->fetch(FetchMode::COLUMN));
    }

    /**
     * Find a limited number of blog articles
     *
     * @param int $offset        Pagination offset
     * @param int $limit         Pagination limit
     * @param array $categories  Categories
     * @param bool $showDisabled Include disabled articles
     * @param int $orderBy       Ordering
     * @param array $storagePids Storage PIDs
     * @param int $count         Overall article count (set by reference)
     *
     * @return QueryResultInterface Blog articles
     * @throws InvalidQueryException
     */
    public function findLimited(
        int $offset = 0,
        int $limit = 1,
        array $categories = [],
        bool $showDisabled = false,
        int $orderBy = self::ORDER_BY_STARTTIME,
        array $storagePids = [],
        int &$count = null
    ): ?QueryResultInterface {
        $count = 0;
        $query = $this->createQuery();
        $orderings = ($orderBy == self::ORDER_BY_SORTING) ?
            [
                'sorting' => QueryInterface::ORDER_ASCENDING
            ] : [
                'starttime' => QueryInterface::ORDER_DESCENDING,
                'uid' => QueryInterface::ORDER_DESCENDING,
            ];
        $storagePids = $storagePids ?: $query->getQuerySettings()->getStoragePageIds();
        $query->statement($this->createQueryStatement(
            [],
            $offset,
            $limit,
            $categories,
            $showDisabled,
            $storagePids,
            $orderings,
            $count
        )->getSQL());

        return $query->execute();
    }

    /**
     * Get next blog article by starttime or uid
     *
     * @param BlogArticle $blogArticle Current blog article
     * @param array $categories        Categories
     *
     * @return null|BlogArticle Next blog article
     * @throws InvalidConfigurationTypeException
     */
    public function findNext(BlogArticle $blogArticle, array $categories = []): ?BlogArticle
    {
        $startTime = $blogArticle->getStarttime();
        $orderings = $startTime ?
            ['starttime' => QueryInterface::ORDER_ASCENDING, 'uid' => QueryInterface::ORDER_ASCENDING] :
            ['uid' => QueryInterface::ORDER_ASCENDING];
        $storagePids = $this->getStoragePids(['blog', 'storagePid']);
        $queryBuilder = $this->createQueryStatement([], 0, 1, $categories, false, $storagePids, $orderings);
        if ($startTime) {
            $queryBuilder->andWhere($queryBuilder->expr()->gt('p.starttime', $startTime));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->gt('p.uid', $blogArticle->getUid()));
        }
        $query = $this->createQuery();
        $query->statement($queryBuilder->getSQL());

        return $query->execute()->getFirst();
    }

    /**
     * Get previous blog article by starttime or uid
     *
     * @param BlogArticle $blogArticle Current blog article
     * @param array $categories        Categories
     *
     * @return null|BlogArticle Previous Blog article
     * @throws InvalidConfigurationTypeException
     */
    public function findPrevious(BlogArticle $blogArticle, array $categories = []): ?BlogArticle
    {
        $startTime = $blogArticle->getStarttime();
        $orderings = $startTime ?
            ['starttime' => QueryInterface::ORDER_DESCENDING, 'uid' => QueryInterface::ORDER_DESCENDING] :
            ['uid' => QueryInterface::ORDER_DESCENDING];
        $storagePids = $this->getStoragePids(['blog', 'storagePid']);
        $queryBuilder = $this->createQueryStatement([], 0, 1, $categories, false, $storagePids, $orderings);
        if ($startTime) {
            $queryBuilder->andWhere($queryBuilder->expr()->lt('p.starttime', $startTime));
        } else {
            $queryBuilder->andWhere($queryBuilder->expr()->lt('p.uid', $blogArticle->getUid()));
        }
        $query = $this->createQuery();
        $query->statement($queryBuilder->getSQL());

        return $query->execute()->getFirst();
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
        $query = $this->createQuery();
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
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $queryGenerator = $objectManager->get(QueryGenerator::class);

        return GeneralUtility::trimExplode(',', $queryGenerator->getTreeList($storagePid, $recursive));
    }
}
