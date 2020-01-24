<?php
/**
 * Tollwerk Blog
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Hooks\TwBlog
 * @author     Klaus Fiedler <klaus@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2020 Klaus Fiedler <klaus@tollwerk.de>
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2020 Klaus Fiedler <klaus@tollwerk.de>, tollwerk® GmbH
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

namespace Tollwerk\TwBlog\Hooks\TwBlog;


use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * CreateQueryStatementInterface
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\Hooks
 */
interface CreateQueryStatementInterface
{
    /**
     * Hook for manipulating the central query builder
     * used by all repository find-methods.
     *
     * @param QueryBuilder $query
     */
    public function createQueryStatement(&$query): void;
}