<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

//\Tx_Extbase_Utility_Extension::configurePlugin(
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'AgenturKonitzer.' . $_EXTKEY,
	'Pi1',
	array(
		'Asset' => 'search,quickSearch,fullTextSearch',
		
	),
	// non-cacheable actions
	array(
		'Asset' => 'search,quickSearch,fullTextSearch',
	)
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AgenturKonitzer.' . $_EXTKEY,
    'Pi2',
    array(
        'RbNews' => 'teaser',
    ),
    // non-cacheable actions
    array(
        'RbNews' => 'teaser',
    )
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$_EXTKEY] =
    'EXT:' . $_EXTKEY . '/Classes/RbAssetArchiveHooks.php:AgenturKonitzer\RbAssetArchive\RbAssetArchiveHooks';
?>