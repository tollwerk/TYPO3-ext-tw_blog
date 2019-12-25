<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function($extKey) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
            $extKey,
            'Configuration/TypoScript/Static',
            'tollwerk Blog'
        );

        // Allow records on standard pages
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_twblog_domain_model_comment');

        // Register blog post list plugin
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Tollwerk.TwBlog',
            'Blog',
            'LLL:EXT:tw_blog/Resources/Private/Language/locallang_db.xlf:plugin.blog',
            'EXT:tw_blog/Resources/Public/Icons/Extension/Blog.svg'
        );

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['twblog_blog'] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            'twblog_blog',
            'FILE:EXT:tw_blog/Configuration/FlexForms/Blog.xml'
        );

        // Add new doktypes
        $archiveDoktype                          = \Tollwerk\TwBlog\Domain\Repository\BlogPostRepository::DOKTYPE;
        $GLOBALS['PAGES_TYPES'][$archiveDoktype] = [
            'type'          => 'web',
            'allowedTables' => '*',
        ];
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList('.$archiveDoktype.')'
        );

        // Register icons in icon factory
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'apps-pagetree-blog',
            TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:'.$extKey.'/Resources/Public/Icons/Extension/apps-pagetree-page-blogpage.svg',]
        );
    },
    'tw_blog'
);
