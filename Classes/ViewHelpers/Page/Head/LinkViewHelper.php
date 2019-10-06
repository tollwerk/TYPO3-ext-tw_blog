<?php

namespace Tollwerk\TwBlog\ViewHelpers\Page\Head;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper used to render a link tag in the `<head>` section of the page.
 * If you use the ViewHelper in a plugin, the plugin and its action have to
 * be cached!
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var    string
     */
    protected $tagName = 'link';

    /**
     * Arguments initialization
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerTagAttribute('rel', 'string', 'Property: rel');
        $this->registerTagAttribute('href', 'string', 'Property: href');
        $this->registerTagAttribute('type', 'string', 'Property: type');
        $this->registerTagAttribute('lang', 'string', 'Property: lang');
        $this->registerTagAttribute('dir', 'string', 'Property: dir');
        $this->registerArgument('priority', 'integer',
            'Numerical priority if multiple calls are made. Highest wins. Default is 1.', false, 1);
    }

    /**
     * Render method
     *
     * @return void
     */
    public function render()
    {
        $GLOBALS['TSFE']->additionalHeaderData['tx_twblog_link_'.$this->tag->getAttribute('rel')] = $this->tag->render();
    }
}
