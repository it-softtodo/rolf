<?php
namespace AgenturKonitzer\RbAssetArchive\Domain\Repository;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 *
 * @package rb_asset_archive
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class AssetRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

//    // set repository wide settings
//    public function initializeObject() {
//        /** @var $defaultQuerySettings Tx_Extbase_Persistence_Typo3QuerySettings */
//        $defaultQuerySettings = $this->objectManager->get('Tx_Extbase_Persistence_Typo3QuerySettings');
//        $defaultQuerySettings->setRespectStoragePage(FALSE);
//        $this->setDefaultQuerySettings($defaultQuerySettings);
//    }

    /**
     * @param array $arguments
     */
    public function filterAssets($arguments) {

        if($_SERVER['HTTP_HOST'] == "mis.huelsta-sofa.com" || $_SERVER['HTTP_HOST'] == "portal.rolf-benz.matrix.de" || $_SERVER['HTTP_HOST'] == "rolf-benz.local" || $_SERVER['HTTP_HOST'] == "hulsta-sofa.local" ){

            return $this->msFilterAssets($arguments);

        }else{
            // init
            $query = $this->createQuery();
            $and = array();
            $and[] = $query->equals('active', 1);

            // from filter
            if ($arguments['periodFromYear']) {
                $from = '01.'.$arguments['periodFromMonth'].'.'.$arguments['periodFromYear'];
                $and[] = $query->greaterThanOrEqual('date', new \DateTime($from));
            }

            // to filter
            if ($arguments['periodToYear']) {
                $to = '31.'.$arguments['periodToMonth'].'.'.$arguments['periodToYear'];
                $and[] = $query->lessThanOrEqual('date', new \DateTime($to));
            }

            // theme filter
            if ($arguments['theme']) {
                $categoryService = GeneralUtility::makeInstance('Tx_News_Service_CategoryService');
                $themesList = $categoryService::getChildrenCategories($arguments['theme']);

                $or = array();
                foreach (GeneralUtility::intExplode(',', $themesList) as $theme) {
                    $or[] = $query->contains('programms', $theme);
                }

                $and[] = $query->logicalOr($or);
            }

            // keyword filter
            if ($arguments['keyword']) {
                $and[] = $query->like('keywords', '%'.$arguments['keyword'].'%');
            }

            // country filter
            if ($arguments['country']) {
                $and[] = $query->contains('countries', $arguments['country']);
            }

            // brand filter
            if ($arguments['brand']) {
                $or = array();
                //foreach ($arguments['brand'] as $brand) {
                    $or[] = $query->equals('brand', $arguments['brand']);
               // }
                $and[] = $query->logicalOr($or);
            }
            // bildtyp filter
            if ($arguments['bildtyp']) {
                $or = array();
                //foreach ($arguments['bildtyp'] as $bildtyp) {
                    $or[] = $query->equals('bildtyp', $arguments['bildtyp']);
                //}
                $and[] = $query->logicalOr($or);
            }


            $constraint = $query->logicalAnd($and); // create where object with AND
            $query->matching($constraint); // use constraint
            $query->setOrderings(array(
                                    'sorting' => \Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING,
                                    'date' => \Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
                                ));

            return $query->execute();
        }
    }

    /**
     * @param array $arguments
     */
    private function msFilterAssets($arguments){

        // init
        $query = $this->createQuery();
        $and = array();
        $and[] = $query->equals('active', 1);
        

        $themes = array();
        $or = array();

        $themes[] = $arguments['bildtyp'];
        $themes[] = $arguments['theme'];
        $or[] = $query->in('programms', $themes);
        $and[] = $query->logicalOr($or);

        if ($arguments["theme"] AND $arguments["bildtyp"]) {

            return $this->getresultsByCategories($themes);

        }elseif ($arguments["theme"] AND !$arguments["bildtyp"] ) {
            return $this->getresultsByCategories($themes,false,true);
        }elseif (!$arguments["theme"] AND $arguments["bildtyp"] ) {
            return $this->getresultsByCategories($themes,true,false);
        }else{
            return $this->getresultsByCategories($themes,true,true);
        }


        //return $this->getresultsByCategories($themes, $themeIsFalse = false, $bildtypIsFalse = false);

    }

    /*
     * to bring results for brands and bildtyp
     */
    public function getResultsByParentId($id) {


        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            'sys_category',
            'deleted=0 AND hidden=0 AND parent='.$id,
            'sorting');

        $allCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $allCategories[] = $row;
        }
        return $allCategories;

    }

     /*
      * to bring images from caretgories
      */
    public function getresultsByCategories($ids, $themeIsFalse = false, $bildtypIsFalse = false) {

         
        $stringids="";

        if (!$themeIsFalse OR !$bildtypIsFalse) {
            
            $i = 0;
            $len = count($ids);
            
            foreach($ids as $id){
                if ($i == $len - 1){
                    if (!empty($id)) {
                        $stringids .= $id;
                    }
                    
                }else{

                    if (!empty($id)) {

                        if($i+1 == $len - 1 AND empty($ids[$i+1])){
                            $stringids .= $id;
                        }else{
                            $stringids .= $id . ",";
                        }

                    }
                    
                }
                $i++;
            }
        }





        $children = array();
        $theme_children = array();
        $bildtyp_children = array();

        if($themeIsFalse){

            $theme_ids = "";

            

             if($_SERVER['HTTP_HOST'] == "mis.huelsta-sofa.com"){

                $theme_children = $this->getResultsByParentId(297);

             }else{
                

                 $theme_children = $this->getResultsByParentId(3);

             }


            $i = 0;
                $len = count($theme_children);
                foreach($theme_children as $id){
                   if ($i == $len - 1){
                       $theme_ids .= $id["uid"];
                   }else{
                       $theme_ids .= $id["uid"] . ",";
                   }
                   $i++;
               }

        }

        if ($bildtypIsFalse) {

            $bildtyp_ids = "";

            $bildtyp_children = $this->getResultsByParentId(301);

               $i = 0;
                $len = count($bildtyp_children);
                foreach($bildtyp_children as $id){
                   if ($i == $len - 1){

                       $bildtyp_ids .= $id["uid"];
                   }else{

                       $bildtyp_ids .= $id["uid"] . ",";
                   }
                   $i++;
               }


        }

        $children = array_merge($theme_children,$bildtyp_children);


        if($themeIsFalse OR $bildtypIsFalse){
            if (!empty($stringids)) {
             $stringids.=",";
            }
        }

        

        $i = 0;
        $len = count($children);
        foreach($children as $id){
           if ($i == $len - 1){
               $stringids .= $id["uid"];
           }else{
               $stringids .= $id["uid"] . ",";
           }
           $i++;
       }


        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            "a.*, COUNT(*) as nbre",
            "tx_rbassetarchive_domain_model_asset a, sys_category s, sys_category sc, tx_rbassetarchive_domain_model_asset_programm_mm p",
            "a.deleted=0 AND a.hidden=0 AND a.uid = p.uid_local AND sc.uid = p.uid_foreign AND s.uid = p.uid_foreign AND s.uid IN($stringids) GROUP BY a.uid HAVING nbre > 1 ORDER BY 1 DESC");

        $allCategories = array();
        while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $allCategories[] = $row["uid"];
        }
        
        $assetsids="";
        $i = 0;
        $len = count($allCategories);
        foreach($allCategories as $id){
            if ($i == $len - 1){
                $assetsids .= $id;
            }else{
                $assetsids .= $id . ",";
            }
            $i++;
        }

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($assetsids);

        $assets = array();

        if(!empty($allCategories)) {
            if ($themeIsFalse and $bildtypIsFalse) {

                $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                    "DISTINCT a.*",
                    "tx_rbassetarchive_domain_model_asset a, tx_rbassetarchive_domain_model_asset_programm_mm p ",
                    "a.deleted=0 AND a.hidden=0 AND a.uid = p.uid_local AND p.uid_local IN($assetsids) ORDER BY 1 DESC ");

                $allCategories = array();
                while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                    $allCategories[] = $row["uid"];
                }

                $assets = array();
                $assetUids = array();

                foreach ($allCategories as $category) {
                    $asset = $this->findByUid($category);
                    $assets[] = $asset;
                    $assetUids[] = $asset->getUid();
                }

            } elseif (!$themeIsFalse and !$bildtypIsFalse) {

                $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                    "DISTINCT a.*",
                    "tx_rbassetarchive_domain_model_asset a, tx_rbassetarchive_domain_model_asset_programm_mm p, tx_rbassetarchive_domain_model_asset_programm_mm ap ",
                    "a.deleted=0 AND a.hidden=0 AND a.uid = p.uid_local AND a.uid = ap.uid_local AND p.uid_local IN($assetsids) AND p.uid_foreign = $ids[0] AND ap.uid_foreign = $ids[1] ORDER BY 1 DESC ");

//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($res);
                $allCategories = array();
                while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                    $allCategories[] = $row["uid"];
                }

                //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($stringids);

                foreach ($allCategories as $category) {
                    $asset = $this->findByUid($category);
                    $assets[] = $asset;
                    $assetUids[] = $asset->getUid();
                }


            } elseif ($themeIsFalse or $bildtypIsFalse) {


                if ($themeIsFalse) {

                    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        "DISTINCT a.*",
                        "tx_rbassetarchive_domain_model_asset a, tx_rbassetarchive_domain_model_asset_programm_mm p ",
                        "a.deleted=0 AND a.hidden=0 AND a.uid = p.uid_local AND p.uid_local IN($assetsids) AND p.uid_foreign = $ids[0] ORDER BY 1 DESC ");

                    $allCategories = array();
                    while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                        $allCategories[] = $row["uid"];
                    }

                    $assets = array();
                    $assetUids = array();

                    foreach ($allCategories as $category) {
                        $asset = $this->findByUid($category);
                        $assets[] = $asset;
                        $assetUids[] = $asset->getUid();
                    }
                } else {


                    $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                        "DISTINCT a.*",
                        "tx_rbassetarchive_domain_model_asset a, tx_rbassetarchive_domain_model_asset_programm_mm p ",
                        "a.deleted=0 AND a.hidden=0 AND a.uid = p.uid_local AND p.uid_local IN($assetsids) AND p.uid_foreign = $ids[1] ORDER BY 1 DESC ");

                    $allCategories = array();
                    while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
                        $allCategories[] = $row["uid"];
                    }

                    $assets = array();
                    $assetUids = array();

                    foreach ($allCategories as $category) {
                        $asset = $this->findByUid($category);
                        $assets[] = $asset;
                        $assetUids[] = $asset->getUid();
                    }
                }

            }

            $assetsObject = NULL;
            if(!empty($assetUids)){
                $assetsObject = $this->findAssetsByUids($assetUids);
            }

        }

        if($_SERVER['HTTP_HOST'] == "portal.rolf-benz.matrix.de" || $_SERVER['HTTP_HOST'] == "rolf-benz.local"){
            return $assetsObject;
        }else{
            return $assets;
        }




    }


    public function findAssetsByUids(array $uids) {
        $query = $this->createQuery();
        $query->matching($query->in('uid', $uids));
        return $query->execute();
    }


}
?>