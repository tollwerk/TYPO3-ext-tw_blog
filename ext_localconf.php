<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

call_user_func(
    function() {
        // Register Extension for flux content element
        \FluidTYPO3\Flux\Core::registerProviderExtensionKey('Tollwerk.TwBlog', 'Content');

        // Register custom configurations for rte_ckeditor
        $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['tx_twblog_domain_model_person.description'] = 'EXT:tw_blog/Configuration/Yaml/CkEditor/tx_twblog_domain_model_person.description.yaml';

        // Register global Fluid namespaces
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['blog']   = ['Tollwerk\\TwBlog\\ViewHelpers'];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['vhs']    = ['FluidTYPO3\\Vhs\\ViewHelpers'];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['formvh'] = ['TYPO3\\CMS\\Form\\ViewHelpers'];

        // Register hook after saving records (tt_content, plugins, pages, everything) in backend.
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][]                   = \Tollwerk\TwBlog\Hooks\TceMainHook::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauthgroup.php']['getDefaultUploadFolder']['tw_blog'] = \Tollwerk\TwBlog\Hooks\BackendUserAuthentication::class.'->getDefaultUploadFolder';

        // Configure plugins
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Tollwerk.TwBlog',
            'Blog',
            ['Blog' => 'list'],
            []
        );
    }
);

