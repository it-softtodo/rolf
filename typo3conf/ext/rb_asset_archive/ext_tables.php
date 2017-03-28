<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Rolf Benz // Bild-Archiv // Recherche'
);

ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Pi2',
    'Rolf Benz // News-Extension // Teaser'
);

$typoVersion =
    class_exists('t3lib_utility_VersionNumber') ?
        \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) :
        \TYPO3\CMS\Core\Utility\GeneralUtility::convertVersionNumberToInteger(TYPO3_version);

$categoryTable = 'sys_category';
$parentField = 'parent';
if ($typoVersion < 6002000) {
    $categoryTable = 'tx_news_domain_model_category';
    $parentField = 'parentcategory';
}

//$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
//$pluginSignature = strtolower($extensionName) . '_pi1';
//$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
//t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/ControllerActions.xml');

ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Rolf Benz // Bild-Archiv');

ExtensionManagementUtility::addLLrefForTCAdescr('tx_rbassetarchive_domain_model_asset', 'EXT:rb_asset_archive/Resources/Private/Language/locallang_csh_tx_rbassetarchive_domain_model_asset.xlf');
ExtensionManagementUtility::allowTableOnStandardPages('tx_rbassetarchive_domain_model_asset');
$TCA['tx_rbassetarchive_domain_model_asset'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset',
		'label' => 'name',
        'thumbnail' => 'image',
        'type' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
        'requestUpdate' => 'brand',
		'searchFields' => 'name,active,image,date,country,brand,',
		'dynamicConfigFile' => ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Asset.php',
		'iconfile' => ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_rbassetarchive_domain_model_asset.gif'
	),
);

# Extend the news extension
$tempColumns = Array (
    'assets' => array(
        'exclude' => 1,
        'l10n_mode' => 'exclude',
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_news.assets',
        'config' => array(
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'tx_rbassetarchive_domain_model_asset',
            'foreign_table' => 'tx_rbassetarchive_domain_model_asset',
            'size' => 8,
            'minitems' => 1,
            'maxitems' => 10,
            'MM' => 'tx_news_domain_model_news_asset_mm',
            'wizards' => array(
                'suggest' => array(
                    'type' => 'suggest',
                ),
            ),
        )
    ),
    'news_id' => Array (
        'exclude' => 1,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_news.news_id',
        'config' => Array (
            'type' => 'input',
            'size' => 13,
            'max' => 4,
            'eval' => 'trim,required'
        )
    ),
    'contact' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_news.contact',
        'config' => array(
            'type' => 'select',
            'items' => array(
                array('LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_misc.pls_choose', 0)
            ),
            'foreign_table' => 'tt_address',
//            'foreign_table_where' => 'AND  tt_address.parentcategory = ###PAGE_TSCONFIG_ID###',
            'minitems' => 0,
            'maxitems' => 1,
        ),
    ),
    'countries' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.country',
        'config' => array(
            'type' => 'select',
            'MM' => 'tx_news_domain_model_news_country_mm',
            'foreign_table' => $categoryTable,
            'foreign_table_where' => ' AND ('.$categoryTable.'.sys_language_uid = 0 OR '.$categoryTable.'.l10n_parent = 0) AND '.$categoryTable.'.'.$parentField.' = ###PAGE_TSCONFIG_ID### ORDER BY '.$categoryTable.'.sorting',
            'size' => 6,
            'autoSizeMax' => 6,
            'minitems' => 0,
            'maxitems' => 6,
        ),
    ),
    'brand' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.brand',
        'config' => array(
            'type' => 'select',
            'items' => array(
                array('LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_misc.pls_choose', 0)
            ),
            'foreign_table' => $categoryTable,
            'foreign_table_where' => 'AND '.$categoryTable.'.'.$parentField.' = ###PAGE_TSCONFIG_ID###',
            'requestUpdate' => 1,
            'minitems' => 0,
            'maxitems' => 1,
        ),
    ),
    'programms' => array(
        'exclude' => 0,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.programm',
        'displayCond' => 'FIELD:brand:!=:0',
        'config' => array(
            'type' => 'select',
            'renderMode' => 'tree',
            'treeConfig' => array(
                'dataProvider' => 'Tx_RbAssetArchive_TreeProvider_DatabaseTreeDataProvider',
                'dependedRootUid' => '###REC_FIELD_brand###',
                'parentField' => $parentField,
                'appearance' => array(
                    'showHeader' => TRUE,
                    'allowRecursiveMode' => TRUE,
                ),
            ),
            'MM' => 'tx_news_domain_model_news_programm_mm',
            'foreign_table' => $categoryTable,
            'foreign_table_where' => ' AND ('.$categoryTable.'.sys_language_uid = 0 OR '.$categoryTable.'.l10n_parent = 0) ORDER BY '.$categoryTable.'.sorting',
            'size' => 10,
            'autoSizeMax' => 20,
            'minitems' => 0,
            'maxitems' => 20,
        ),
    ),
    'headline_2ndline' => Array (
        'exclude' => 1,
        'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_news.headline_2ndline',
        'config' => Array (
            'type' => 'input',
            'eval' => 'trim'
        )
    ),
);


$ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xml:';
$TCA['tx_news_domain_model_news']['types']['0']['showitem']=
    'l10n_parent, l10n_diffsource,
        title;;paletteCore,;;;;2-2-2, teaser;;paletteNavtitle,;;;;3-3-3,author;;paletteAuthor,datetime;;paletteArchive,
        bodytext;;;richtext::rte_transform[flag=rte_disabled|mode=ts_css],
        rte_disabled;LLL:EXT:cms/locallang_ttc.xml:rte_enabled_formlabel,
        content_elements,

    --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
        --palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;paletteAccess,

    --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.options,tags,keywords,
    --div--;' . $ll . 'tx_news_domain_model_news.tabs.relations,media,related_files,related_links,related,related_from,
                --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.extended,'
;

$TCA['tx_news_domain_model_news']['types'][3] = array(
    'showitem' => 'l10n_parent, l10n_diffsource,
					title;;paletteHeadline,;;;;2-2-2,media,teaser;;paletteNavtitle,;;;;3-3-3,author;;paletteAuthor,datetime;;paletteArchive,
					bodytext;;;richtext::rte_transform[flag=rte_disabled|mode=ts_css],
					rte_disabled;LLL:EXT:cms/locallang_ttc.xml:rte_enabled_formlabel,
					content_elements,

				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
					--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;paletteAccess,

				--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.options,categories,tags,keywords,
				--div--;' . $ll . 'tx_news_domain_model_news.tabs.relations,related_files,related_links,related,related_from,
				--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.extended,'
);

// Media-Element der News-Extension umbenennen
$TCA['tx_news_domain_model_news']['columns']['media']['label'] = 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_news.media';


$defaultConfig = $TCA["tx_news_domain_model_news"]["columns"]["categories"]["config"]["foreign_table_where"];
$additionalConfig = ' AND '.$categoryTable.'.pid = ###STORAGE_PID###';
$TCA["tx_news_domain_model_news"]["columns"]["categories"]["config"]["foreign_table_where"] = $additionalConfig.$defaultConfig;

$item = array("LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_news.type.I.3", 3);
$relativeToField = "LLL:EXT:news/Resources/Private/Language/locallang_db.xml:tx_news_domain_model_news.type.I.0";
ExtensionManagementUtility::addTcaSelectItem("tx_news_domain_model_news", "type", $item, $relativeToField, "after");

GeneralUtility::loadTCA("tx_news_domain_model_news");
ExtensionManagementUtility::addTCAcolumns("tx_news_domain_model_news",$tempColumns,1);

ExtensionManagementUtility::addToAllTCAtypes("tx_news_domain_model_news","countries,brand,programms", "0,1,2", "before:tags");
ExtensionManagementUtility::addToAllTCAtypes("tx_news_domain_model_news","news_id,contact", "0,1,2");

$paletteCore = $TCA["tx_news_domain_model_news"]["palettes"]["paletteCore"]["showitem"];
ExtensionManagementUtility::addFieldsToPalette("tx_news_domain_model_news","paletteHeadline","headline_2ndline,--linebreak--,".$paletteCore, "before:istopnews");

ExtensionManagementUtility::addToAllTCAtypes("tx_news_domain_model_news","--div--;LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_tca.xml:pages.tabs.images,assets");
?>