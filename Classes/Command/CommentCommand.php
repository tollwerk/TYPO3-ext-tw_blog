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
 *  Copyright © 2020 Joschi Kuphal <joschi@tollwerk.de>
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

namespace Tollwerk\TwBlog\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tollwerk\TwBlog\Domain\Repository\CommentRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Comment Command
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Command
 */
class CommentCommand extends Command
{
    /**
     * Object manager
     *
     * @var ObjectManager
     */
    protected $objectManager;
    /**
     * Persistence Manager
     *
     * @var PersistenceManager
     */
    protected $persistenceManager;
    /**
     * Comment repository
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * Initialize the command
     *
     * @param InputInterface $input   An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws Exception
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        // Instantiate the repositories etc.
        $this->objectManager      = GeneralUtility::makeInstance(ObjectManager::class);
        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
        $this->commentRepository  = $this->objectManager->get(CommentRepository::class);
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure()
    {
        $this->setDescription('Clean blog comments');
        $this->setHelp('Scan all blog comments, drop unconfirmed ones and anonymize older ones');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input   An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
                                    ->getConnectionForTable('tx_twblog_domain_model_comment');

        // Auto-confirm all visible comments
        $connection->update('tx_twblog_domain_model_comment', ['confirmation' => ''], ['hidden' => 0]);

        // Get a query builder
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                                      ->getQueryBuilderForTable('tx_twblog_domain_model_comment');
        $queryBuilder->getRestrictions()->removeAll();

        // Delete unconfirmed comments that are older than 1 month
        $queryBuilder->delete('tx_twblog_domain_model_comment')
                     ->where(
                         $queryBuilder->expr()->andX(
                             $queryBuilder->expr()->neq('confirmation', '""'),
                             $queryBuilder->expr()->lt('crdate', time() - 31 * 86400)
                         )
                     )->execute();

        // Delete IP addresses if blog posts that haven't been changed for 2 months minimum
        $queryBuilder->update('tx_twblog_domain_model_comment')
                     ->set('ip', '')
                     ->where($queryBuilder->expr()->lt('tstamp', time() - 60 * 86400))
                     ->execute();

        return 0;
    }
}
