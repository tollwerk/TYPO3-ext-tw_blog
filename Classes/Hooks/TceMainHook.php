<?php
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Tollwerk\TwBlog\Hooks;

use Tollwerk\TwBlog\Domain\Model\BlogArticle;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Datahandler hooks
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Hooks
 */
class TceMainHook
{
    protected function checkMultiFuzzyUnique($table, $columns, $id, &$fieldArray)
    {
        $recordFound = Evaluator::multiFuzzyUnique($table, $columns, $id);
        if ($recordFound) {
            $fieldArray = [];
            FlashMessager::addFlashMessage(
                LocalizationUtility::translate('evaluation.fuzzyUnique.text', 'TwAfg',
                    [$recordFound['uid'], $recordFound['pid'], implode(', ', array_keys($columns))]),
                LocalizationUtility::translate('evaluation.fuzzyUnique.title', 'TwAfg'),
                FlashMessage::WARNING
            );

            return $recordFound;
        }

        return null;
    }

    /**
     * Pre-process hook
     *
     * @param array $fieldArray
     * @param $table
     * @param $id
     * @param DataHandler $pObj
     */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, DataHandler &$pObj)
    {
        switch ($table) {
            // Pages
            case 'pages':
                if ((intval($fieldArray['doktype']) == BlogArticle::DOKTYPE) && empty($fieldArray['starttime'])) {
                    if (is_int($id)) {
                        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                                                      ->getQueryBuilderForTable('pages');
                        /** @var \Doctrine\DBAL\Driver\Mysqli\MysqliStatement $result */
                        $statement               = $queryBuilder->select('uid', 'starttime', 'crdate')
                                                                ->from('pages')
                                                                ->where($queryBuilder->expr()->eq('uid', $id))
                                                                ->execute();
                        $row                     = $statement->fetch();
                        $fieldArray['starttime'] = (new \DateTimeImmutable('@'.(intval($row['crdate']) ?: time())))->format('c');
                    } else {
                        $fieldArray['starttime'] = (new \DateTimeImmutable())->format('c');
                    }
                }
                break;
        }
    }
}
