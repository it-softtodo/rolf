<?php

namespace AgenturKonitzer\RbAssetArchive\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Martin Lazar-Rudolf <martin@lazar-rudolf.de>, Agentur Konitzer
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

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \AgenturKonitzer\RbAssetArchive\RbAssetArchiveHooks;
use \AgenturKonitzer\RbAssetArchive\Domain\Repository\NewsRepository;

/**
 *
 *
 * @package rb_asset_archive
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AssetController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * assetRepository
	 *
	 * @var \AgenturKonitzer\RbAssetArchive\Domain\Repository\AssetRepository
	 * @inject
	 */
	protected $assetRepository;

    /**
     * newsRepository
     *
     * @var \AgenturKonitzer\RbAssetArchive\Domain\Repository\NewsRepository
     * @inject
     */
    protected $newsRepository;

    /**
     * @var string
     */
    protected $parentField;


    /**
     * Mapping für Sprach-ID zu Regionen
     */
    private static $languageMapping = array(1 => 28, 2 => 29, 3 => 30, 4 => 31, 5 => 32, 6 => 33);

    /**
     * Marken-Konstante
     */
    private static $brands = array('checkbox_rb' => 1, 'checkbox_fs' => 2);

    public function initializeAction() {
        session_start();
        parent::initializeAction();
        setlocale(LC_ALL, $GLOBALS['TSFE']->config['config']['locale_all'].'.utf8');
    }


    /**
     * action Search
     *
     * @return void
     */
    public function searchAction() {


        // Lösung f. Firefox-Problem mit "Dokument erloschen"
        header('Cache-Control: max-age=600');

        /**********************************************************************************************************
         * Search-Box befüllen
         **********************************************************************************************************/
        $types = array(LocalizationUtility::translate('tx_rbassetarchive.form.pls_choose', $this->extensionName));
        foreach(array(1, 2) as $item) {
            $types[] = LocalizationUtility::translate('tx_rbassetarchive.types.'.$item, $this->extensionName);
        }

        $months = range(1, 12);
        $monthNames = array();
        foreach ($months as $month)  {
            $monthNames[] = strftime('%B', strtotime('1.'.$month.'.1990'));
        }
        $periodFromMonth = array_combine($months, $monthNames);
        $periodFromMonth = array_merge(array(0 => LocalizationUtility::translate('tx_rbassetarchive.form.all', $this->extensionName)),
                                       $periodFromMonth);
        $years = range(date('Y') - 6, date('Y'));
        $periodFromYear = array_combine($years, $years);
        $periodFromYear = array(0 => LocalizationUtility::translate('tx_rbassetarchive.form.all', $this->extensionName)) + $periodFromYear;
        $periodToMonth = $periodFromMonth;
        $periodToYear = $periodFromYear;

        $themes = array();
        $themes[] = array('title' => LocalizationUtility::translate('tx_rbassetarchive.form.all_themes', $this->extensionName),
                          'id' => 0,
                          'level' => 0);

        // Alles unterhalb von Marken
        $themes = array_merge($themes, $this->getCategories($this->settings['brandsId']));

        // Übersetzungen einfügen
        foreach ($themes as $key => $theme) {
            $title = $theme['title'];
            $localizedTitle = LocalizationUtility::translate($title, $this->extensionName);
            if (!empty($localizedTitle)) {
                $title = $localizedTitle;
            }
            $themes[$key]['title'] = '- '.$title;
        }

        $this->view->assign('types', $types);

        $this->view->assign('periodFromMonth', $periodFromMonth);
        $this->view->assign('periodFromYear', $periodFromYear);
        $this->view->assign('periodToMonth', $periodToMonth);
        $this->view->assign('periodToYear', $periodToYear);


        if (array_key_exists('tx_rb_asset_archive_pi1', $_GET) && array_key_exists('more', $_GET['tx_rb_asset_archive_pi1'])) {
            $this->request->setArgument('type', $_GET['tx_rb_asset_archive_pi1']['type']);
            $this->request->setArgument('theme', $_GET['tx_rb_asset_archive_pi1']['theme']);
            $this->request->setMethod('POST');
        }


        $this->view->assign('themes', $themes);

        

        if($_SERVER['HTTP_HOST'] == "mis.huelsta-sofa.com" || $_SERVER['HTTP_HOST'] == "portal.rolf-benz.matrix.de"){

            $this->msSearchAction();

        }
        if ($this->request->getMethod() == 'POST') {
            unset($_SESSION['arguments']);
			$firstvisit = 0;
            // Wird nach Bilder oder Pressemitteilungen gesucht
            $type = $this->request->getArgument('type');


            // Region anhand der Sprache setzen
            if ($GLOBALS['TSFE']->sys_language_uid != 0) {
                $this->request->setArgument('country', self::getRegionBySyslanguageUid($GLOBALS['TSFE']->sys_language_uid));
            }

            $this->request->setArgument('brands', self::getBrands());

            $news = array();
            $assets = array();
            switch ($type) {
                case 1:
                    $news = $this->listNews();
                    break;
                case 2:
                    $assets = $this->listAssets();
                    break;
                default:
                    $this->redirect('search');
            }


            $this->view->assign('news', $news);
            $this->view->assign('assets', $assets);
            $this->view->assign('firstvisit', $firstvisit);
            $this->view->assign('arguments', $this->request->getArguments());

            $_SESSION['arguments'] = $this->request->getArguments();

        } else {
            // Default-Ansicht (keine Parameter in Query)

            if ((count($this->request->getArguments()) == 0 || !$this->request->hasArgument('type')) && count($_SESSION['arguments']) == 0) {
                $news = $this->listDefaultNews();
                $this->view->assign('news', $news);

            } else { // Paginating aktiv

                $searchArguments = array('type');

                foreach ($searchArguments as $argument) {
                    $this->request->setArgument($argument, $_SESSION['arguments'][$argument]);
                }

                // Wird nach Bilder oder Pressemitteilungen gesucht
                $type = $this->request->getArgument('type');

                $news = array();
                $assets = array();
                switch ($type) {
                    case 1:
                        $news = $this->listNews();
                        break;
                    case 2:
                        $assets = $assets;
                        $firstvisit = 0;
                        break;
                    default:
                        $this->redirect('search');
                }

                $this->view->assign('news', $news);
                $this->view->assign('assets', $assets);
                $this->view->assign('firstvisit', $firstvisit);
                $this->view->assign('arguments', $this->request->getArguments());
            }
        } 
    }

    /**
     * action Search
     *
     * @return void
     */
    private function msSearchAction(){

        $firstvisit = 1;
        $misHS_themes = array();
        $misHS_themes[] = array('title' => LocalizationUtility::translate('tx_rbassetarchive.form.all_themes', $this->extensionName),
                          'id' => 0,
                          'level' => 0);

        // Alles unterhalb von Marken
        if($_SERVER['HTTP_HOST'] == "mis.huelsta-sofa.com"){
            $misHS_themes = array_merge($misHS_themes, $this->findChildren($this->settings['MisHS']["value"]));
        }else{
            $pageid = intval($GLOBALS['TSFE']->id);
            if($pageid == $this->settings['RBPID']["value"]){
                $misHS_themes = array_merge($misHS_themes, $this->findChildren($this->settings['RB']["value"]));

            }else{
                $misHS_themes = array_merge($misHS_themes, $this->findChildren($this->settings['Freistil']["value"]));
            }
            
        }
        // Übersetzungen einfügen
        foreach ($misHS_themes as $key => $theme) {
            $title = $theme['title'];
            $localizedTitle = LocalizationUtility::translate($title, $this->extensionName);
            if (!empty($localizedTitle)) {
                $title = $localizedTitle;
            }
            $misHS_themes[$key]['title'] = '- '.$title;
        }

        //$this->view->assign('types', $types);

        $markens_brands[] = array('title' => LocalizationUtility::translate('tx_rbassetarchive.form.all_themes', $this->extensionName),
            'id' => 0,
            'level' => 0);

        $markens_brands = array_merge($markens_brands, $this->assetRepository->getResultsByParentId($this->settings['brandsId']));

        foreach ($markens_brands as $key => $theme) {
            $title = $theme['title'];
            $localizedTitle = LocalizationUtility::translate($title, $this->extensionName);
            if (!empty($localizedTitle)) {
                $title = $localizedTitle;
            }
            $markens_brands[$key]['title'] = '- '.$title;
        }


        $bildtyps[] = array('title' => LocalizationUtility::translate('tx_rbassetarchive.form.all_themes', $this->extensionName),
            'id' => 0,
            'level' => 0);

        $bildtyps = array_merge($bildtyps, $this->assetRepository->getResultsByParentId($this->settings['bildtypId']));

        foreach ($bildtyps as $key => $theme) {
            $title = $theme['title'];
            $localizedTitle = LocalizationUtility::translate($title, $this->extensionName);
            if (!empty($localizedTitle)) {
                $title = $localizedTitle;
            }
            $bildtyps[$key]['title'] = '- '.$title;
        }



        $this->view->assign('markens_brands', $markens_brands);
        $this->view->assign('bildtyps', $bildtyps);




        $countthemes = count($misHS_themes);

        for ($i= 1 ; $i < $countthemes ; $i++) {

            if ($misHS_themes[$i]["isparent"]== 1) {

                $misHS_themes[$i]["level"] = 25;

            }else{
               
               $misHS_themes[$i]["level"] = 50;
           }
            
        }
//var_dump($misHS_themes);
        $this->view->assign('mis_themes', $misHS_themes);       

    }




    /**
     * gibt die Region-ID zurück
     *
     * @param $langUid
     * @return int Region-Id
     */
    public static function getRegionBySyslanguageUid($langUid) {
        return self::$languageMapping[$langUid];
    }

    /**
     * gibt die Region-ID zurück
     *
     * @param $input CookieParameter
     * @return int Brand-Id
     */
    public static function getBrand($input) {
        return self::$brands[$input];
    }

    public static function getBrands() {
        // Marken setzen
        $brands = array();
        if (!empty($_COOKIE['markenanzeige'])) {
            $cookieValues = explode(',', $_COOKIE['markenanzeige']);
            foreach($cookieValues as $cookieValue) {
                $brands[] = self::getBrand($cookieValue);
            }
        } else {
            foreach(self::$brands as $brand) {
                $brands[] = $brand;
            }
        }
        return $brands;
    }

    /**
     * action quickSearch
     *
     * @return void
     */
    public function quickSearchAction() {
        // Region anhand der Sprache setzen
        if ($GLOBALS['TSFE']->sys_language_uid != 0) {
            $this->request->setArgument('country', self::getRegionBySyslanguageUid($GLOBALS['TSFE']->sys_language_uid));
        }

        $this->request->setArgument('brands', self::getBrands());

        if ($this->request->hasArgument('newsId')) {
            $newsId = $this->request->getArgument('newsId');
            if (!empty($newsId)) {
                $newsItem = $this->newsRepository->findNewsByNewsId($this->request->getArguments());
                if ($newsItem) { // Wenn etwas gefunden wurde, dann auf News-Detailseite weiterleiten
                    $this->redirect('detail', 'News', 'News', array('news' => $newsItem), 14);
                }
            }
        }
        // Nichts gefunden: Ausgabe des entsprechenden Hinweises in Default-Template
    }

    /**
     * action fullTextSearch
     *
     * @return void
     */
    public function fullTextSearchAction() {
        // Region anhand der Sprache setzen
        if ($GLOBALS['TSFE']->sys_language_uid != 0) {
            $this->request->setArgument('country', self::getRegionBySyslanguageUid($GLOBALS['TSFE']->sys_language_uid));
        }

        $this->request->setArgument('brands', self::getBrands());

        if ($this->request->hasArgument('searchText')) {
            $searchText = $this->request->getArgument('searchText');
            if (!empty($searchText)) {
                $news = $this->newsRepository->findBySearchText($this->request->getArguments());
                $this->view->assign('news', $news);
            }
        }
    }


    private  function listAssets() {

        //var_dump($this->request->getArguments());

        $assets = $this->assetRepository->filterAssets($this->request->getArguments());

        foreach ($assets as $asset) {
            $fileInfo = \TYPO3\CMS\Core\Utility\GeneralUtility::split_fileref($asset->getImage());
            $orgFileName = preg_replace('/_[0-9][0-9]$/', '', $fileInfo['filebody']).'_';
            $asset->setThumbL(RbAssetArchiveHooks::BASIC_PATH.RbAssetArchiveHooks::THUMB_LARGE.'/'.$orgFileName.RbAssetArchiveHooks::THUMB_LARGE.'.jpg');
            $asset->setLarge(RbAssetArchiveHooks::BASIC_PATH.RbAssetArchiveHooks::LARGE.'/'.$orgFileName.RbAssetArchiveHooks::LARGE.'.jpg');
            $asset->setLarge540(RbAssetArchiveHooks::BASIC_PATH.RbAssetArchiveHooks::LARGE540.'/'.$orgFileName.RbAssetArchiveHooks::LARGE540.'.jpg');
        }



        return $assets;
    }

    private  function listDefaultNews() {
        $countryId = 0;
        if ($GLOBALS['TSFE']->sys_language_uid != 0) {
            $countryId = self::getRegionBySyslanguageUid($GLOBALS['TSFE']->sys_language_uid);
        }
        $news = $this->newsRepository->findLatest(intval($this->settings['numberOfNewsInArchive']) < 1 ? 1 : intval($this->settings['numberOfNewsInArchive']), $countryId, self::getBrands());
        return $news;
    }

    private  function listNews() {
        $news = $this->newsRepository->filterNews($this->request->getArguments());
        return $news;
    }

	/**
	 * action show
	 *
	 * @param \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $asset
	 * @return void
	 */
	public function showAction(\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $asset) {
		$this->view->assign('asset', $asset);
	}


    /**
     * findChildren By parent Id
     *
     * @param int $id
     * @return array $categories
     */
    private function findChildren($id){

        $categoryTable = 'sys_category';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            $categoryTable,
            ' parent = '. $id . ' And deleted=0 AND hidden=0');
        $allParentCategs = array();
        $allCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $allCategories[] = $row["uid"];
            $allParentCategs[] = $row;
        }

        foreach ($allParentCategs as $key => $value) {
            $allParentCategs[$key]["isparent"] = 1;
        }



        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($allParentCategs);

        $stringids=$id .",";
        $i = 0;
        $len = count($allCategories);
        foreach($allCategories as $id){
            if ($i == $len - 1){
                $stringids .= $id;
            }else{
                $stringids .= $id . ",";
            }
            $i++;
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            $categoryTable,
            ' parent IN( '. $stringids . ') And deleted=0 AND hidden=0 ' );

        $allCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $allCategories[] = $row;
        }

        $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            $categoryTable,
            ' parent IN( '. $stringids . ') And deleted=0 AND hidden=0 GROUP BY parent');

        $allParentCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) {
            $allParentCategories[] = $row;
        }

        foreach ($allCategories as $key => $cat) {
            foreach ($allParentCategories as $cle => $parent) {
                if ( $cat["uid"] == $parent["uid"]) {                    
                    $allCategories[$key]["isparent"] = 1;
                   
                }
            }
        }

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($allParentCategs);

        $finalCategs = array();

        foreach ($allParentCategs as $key => $value) {

            $res = $GLOBALS['TYPO3_DB']->sql_query('SELECT * from '.$categoryTable.' WHERE uid = '.$value["uid"] . ' AND deleted=0 AND hidden=0 UNION SELECT * FROM '.$categoryTable.' WHERE parent = '.$value["uid"].' AND deleted=0 AND hidden=0');
            
            while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                $finalCategs[] = $row;
            }
            
        }

        foreach ($allParentCategs as $key => $value) {
            foreach ($finalCategs as $k => $v) {
                if($value["uid"] == $v["uid"]){
                    $finalCategs[$k]["isparent"] =1;
                }
            }
        }

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($finalCategs);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($allCategories);
        return $finalCategs;

    }


    private function getCategories($pid) {
        $additionalWhere = '';
        $join = '';


        $typoVersion =
            class_exists('t3lib_utility_VersionNumber') ?
                \t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) :
                \t3lib_div::int_from_ver(TYPO3_version);

        $categoryTable = 'sys_category';
        $this->parentField = 'parent';
        if ($typoVersion < 6002000) {
            $categoryTable = 'tx_news_domain_model_category';
            $this->parentField = 'parentcategory';
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            $categoryTable,
            'deleted=0 AND hidden=0' . $additionalWhere,
            'sorting');

        $allCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $allCategories[] = $row;
        }

        $tree = $this->buildTree($allCategories, $pid);

        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($tree));
        $titles = array();
        $levels = array();
        $parent = array();
        foreach($it as $key => $value) {
            if ($key == 'uid') {
                $temp['uid'] = $value;
            }
            if ($key == 'title') {
                $temp['title'] = $value;
            }
            if ($key == 'level') {
                $titles[$temp['uid']] = $temp['title'];
                $levels[$temp['uid']] = $value*25;
                $temp = array();
            }
        }

//        $join = ' JOIN tx_news_domain_model_news_programm_mm np ON c.uid = np.uid_foreign';
        $join = '';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'c.*',
            $categoryTable.' c'.$join,
            '');

        $filteredCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $filteredCategories[$row['uid']] = $row;
            $parent[] = $row[$this->parentField];
        }

        $themes = array();
        foreach ($titles as $key => $value) {
            if (array_key_exists($key, $filteredCategories) || $levels[$key] == 25 && in_array($key, $parent) || $levels[$key] == 0) {
                $themes[] = array('title' => $value, 'id' => $key, 'level' => $levels[$key], 'status' => 'active');
            }
        }

        return $themes;
    }


    private function buildTree( $ar, $pid = 0, $level = 0) {
        $op = array();
        foreach( $ar as $item ) {
            if($item[$this->parentField] == $pid ) {

                $temp = array(
                    'uid' => $item['uid'],
                    'parent' => $item[$this->parentField],
                    'title' => $item['title'],
                    'level' => $level,
                );

                // using recursion
                $children =  $this->buildTree( $ar, $item['uid'], $level+1);
                if( $children ) {
                    $temp['children'] = $children;
                }
                $op[] = $temp;
            }
        }
        return $op;
    }


}
?>