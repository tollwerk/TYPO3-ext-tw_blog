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

use Tollwerk\TwBlog\Domain\Model\Organization;
use Tollwerk\TwBlog\Domain\Repository\Traits\StoragePidsTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Abstract repository base
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Domain\Repository
 */
abstract class AbstractRepository extends Repository
{
    /**
     * Use the storage PIDs trait
     */
    use StoragePidsTrait;

    /**
     * Returns all objects of this repository.
     *
     * @param array $storagePids Optional: Storage PIDs
     *
     * @return QueryResultInterface|array
     */
    public function findAll(array $storagePids = [])
    {
        if (empty($storagePids)) {
            return parent::findAll();
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setStoragePageIds($storagePids);

        return $query->execute();
    }

    /**
     * Find Records by category IDs
     *
     * @param array $categoryIds      Category IDs
     * @param array|null $storagePids Storage PIDs (optional)
     *
     * @return array|null|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByCategories($categoryIds = [], array $storagePids = null)
    {
        if (!count($categoryIds)) {
            return null;
        }

        $query       = $this->createQuery();
        $constraints = [];
        foreach ($categoryIds as $categoryId) {
            if ($categoryId instanceof AbstractEntity) {
                $categoryId = $categoryId->getUid();
            }
            $constraints[] = $query->contains('categories', $categoryId);
        }
        $query->matching($query->logicalOr($constraints));

        if (!empty($storagePids)) {
            $query->getQuerySettings()->setStoragePageIds($storagePids);
        }

        return $query->execute();
    }

    /**
     * Find by multiple IDs
     *
     * @param array $uids ID list
     *
     * @return null|array|QueryResultInterface Query result
     * @throws InvalidQueryException
     */
    public function findByUids($uids = [])
    {
        if (!is_array($uids) || !count($uids)) {
            return null;
        }

        // Get the records
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->in('uid', $uids));
        $records = $query->execute();

        // Sort manually by order of selected uids
        $recordsByUid = [];
        /** @var AbstractEntity $record */
        foreach ($records as $record) {
            $recordsByUid[$record->getUid()] = $record;
        }
        $return = [];
        foreach ($uids as $uid) {
            $return[] = $recordsByUid[$uid];
        }

        return $return;
    }
}
