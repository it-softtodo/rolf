<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_rbassetarchive_domain_model_asset'] = array(
	'ctrl' => $TCA['tx_rbassetarchive_domain_model_asset']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, name, keywords, active, image, thumb, border, date, countries, brand,bildtyp, programms',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, name, keywords, active, image, thumb, border, date, countries, brand, bildtyp, programms, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_rbassetarchive_domain_model_asset',
				'foreign_table_where' => 'AND tx_rbassetarchive_domain_model_asset.pid=###CURRENT_PID### AND tx_rbassetarchive_domain_model_asset.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
        'keywords' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.keywords',
            'config' => array(
                'type' => 'text',
				'cols' => 30,
				'rows' => 5,
            ),
        ),
		'active' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.active',
			'config' => array(
				'type' => 'check',
				'default' => 1
			),
		),
		'image' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.image',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
                'uploadfolder' => '',
				'show_thumbs' => 1,
				'size' => 1,
                'minitems' => 1,
				'allowed' => 'gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai,zip',
				'disallowed' => '',
                'eval' => 'required'
			),
		),
        'thumb' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.thumb',
            'config' => array(
                'type' => 'group',
                'internal_type' => 'file',
                'uploadfolder' => '',
                'show_thumbs' => 1,
                'size' => 1,
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'disallowed' => '',
            ),
        ),
        'border' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.border',
            'config' => array(
                'type' => 'check',
                'default' => 0
            ),
        ),
        'basic_file_name' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.basic_file_name',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
		'date' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.date',
			'config' => array(
				'type' => 'input',
				'size' => 10,
				'eval' => 'datetime,required',
				'checkbox' => 1,
				'default' => time()
			),
		),
		'countries' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.country',
			'config' => array(
				'type' => 'select',
                'MM' => 'tx_rbassetarchive_domain_model_asset_country_mm',
				'foreign_table' => 'sys_category',
                'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) AND sys_category.parent = ###PAGE_TSCONFIG_ID### ORDER BY sys_category.sorting',
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
				'foreign_table' => 'sys_category',
                'foreign_table_where' => 'AND sys_category.parent = ###PAGE_TSCONFIG_ID###',
                'requestUpdate' => 1,
                'minitems' => 0,
                'maxitems' => 1,
			),
		),




		'programms' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:rb_asset_archive/Resources/Private/Language/locallang_db.xlf:tx_rbassetarchive_domain_model_asset.programm',
			'config' => array(
				'type' => 'select',
				'renderMode' => 'tree',
				'treeConfig' => array(
					'dataProvider' => 'Tx_News_TreeProvider_DatabaseTreeDataProvider',
					'dependedRootUid' => '###REC_FIELD_brand###',
					'parentField' => 'parent',
					'appearance' => array(
						'showHeader' => TRUE,
						'allowRecursiveMode' => TRUE,
						'expandAll' => TRUE,
						'maxLevels' => 99,
					),
				),
				'MM' => 'tx_rbassetarchive_domain_model_asset_programm_mm',

				'foreign_table' => 'sys_category',
				'foreign_table_where' => '  AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
				'size' => 10,
				'autoSizeMax' => 20,
				'minitems' => 0,
				'maxitems' => 20,
			)
		),
	),
);

?>