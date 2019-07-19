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
     * Return array with default constraints that should be used for all queries
     * like only finding pages with the right doktype (116)
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
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
     * @param int $storagePid
     * @param int $recursive
     *
     * @return array
     */
    protected function getStoragePidsRecursive($storagePid = 1, $recursive = 99): array
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $queryGenerator = $objectManager->get(QueryGenerator::class);

        return GeneralUtility::trimExplode(',', $queryGenerator->getTreeList($storagePid, $recursive));
    }

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
     * Find a limited number of blog articles
     *
     * @param int $offset Offset
     * @param int $limit  Limit
     * @param bool $showDisabled
     *
     * @return array|QueryResultInterface Blog articles
     */
    public function findLimited(int $offset = 0, int $limit = 1, int $orderBy = self::ORDER_BY_STARTTIME, bool $showDisabled = false): ?QueryResultInterface
    {
        $query = $this->createQuery();
        if ($showDisabled) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        switch ($orderBy){
            case self::ORDER_BY_SORTING:
                $query->setOrderings([
                    'sorting' => QueryInterface::ORDER_ASCENDING,
                ]);
                break;
            default:
                $query->setOrderings([
                    'starttime' => QueryInterface::ORDER_DESCENDING,
                    'uid' => QueryInterface::ORDER_DESCENDING,

                ]);
                break;
        }

        $query->setOffset($offset);
        $query->setLimit($limit);
        $constraints = $this->getDefaultConstraints($query);
        $return = $query->matching($query->logicalAnd($constraints))->execute();

        return $return;
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
                'uid' => QueryInterface::ORDER_ASCENDING
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
}
