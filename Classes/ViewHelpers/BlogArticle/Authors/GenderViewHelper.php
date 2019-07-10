<?php

/**
 * data
 *
 * @category   Tollwerk
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\ViewHelpers
 * @author     Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright  Copyright © 2018 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @license    http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2018 tollwerk GmbH <info@tollwerk.de>
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

namespace Tollwerk\TwBlog\ViewHelpers\BlogArticle\Authors;

use Tollwerk\TwBlog\Domain\Model\Person;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Return the gender for a list of blog authors / interview partners
 *
 * @package    Tollwerk\TwBlog
 * @subpackage Tollwerk\TwBlog\ViewHelpers\Page
 */
class GenderViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('persons', 'array', 'List of blog authors / interview partners', true);
    }

    /**
     * Return the gender for a list of blog authors / interview partners
     *
     * @return string Gender string
     * @api
     */
    public function render()
    {
        $genders = ['male' => 0, 'female' => 0];
        /** @var Person $person */
        foreach ($this->arguments['persons'] as $person) {
            ++$genders[($person->getGender() == 2) ? 'female' : 'male'];
        }

        if ($genders['male'] && $genders['female']) {
            return 'mixed';
        } elseif ($genders['male'] > 1) {
            return 'males';
        } elseif ($genders['male']) {
            return 'male';
        } elseif ($genders['female'] > 1) {
            return 'females';
        }

        return 'female';
    }
}
