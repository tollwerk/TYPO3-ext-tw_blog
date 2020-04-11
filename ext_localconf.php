<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(
    function() {
        // Register global Fluid namespaces
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['blog']   = ['Tollwerk\\TwBlog\\ViewHelpers'];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vhs']    = ['FluidTYPO3\\Vhs\\ViewHelpers'];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['formvh'] = ['TYPO3\\CMS\\Form\\ViewHelpers'];

        // Register hook after saving records (tt_content, plugins, pages, everything) in backend.
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Tollwerk\TwBlog\Hooks\TceMainHook::class;

        // Configure plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'TwBlog',
            'Blog',
            [\Tollwerk\TwBlog\Controller\BlogController::class => 'list, filter, navigation, atom'],
            []
        );
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'TwBlog',
            'BlogTeaser',
            [\Tollwerk\TwBlog\Controller\BlogController::class => 'teaser'],
            []
        );
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'TwBlog',
            'Comment',
            [\Tollwerk\TwBlog\Controller\CommentController::class => 'confirm'],
            []
        );

        // Extend classes (XCLASSes)
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Model\Category::class]                = [
            'className' => \Tollwerk\TwBlog\Domain\Model\Category::class
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository::class] = [
            'className' => \Tollwerk\TwBlog\Domain\Repository\CategoryRepository::class
        ];

        // Register a custom Query Factory
        $extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\Container\Container::class
        );
        $extbaseObjectContainer->registerImplementation(
            \TYPO3\CMS\Extbase\Persistence\Generic\QueryFactoryInterface::class,
            \Tollwerk\TwBlog\Persistence\QueryFactory::class
        );

        // Prepare hooks for comment confirmation
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['blogComment']['confirmation'] = (array)($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['blogComment']['confirmation'] ?? []);
    }
);
