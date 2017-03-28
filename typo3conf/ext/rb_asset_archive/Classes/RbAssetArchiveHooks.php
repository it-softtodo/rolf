<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 19.08.13
 * Zeit: 14:59
 *
 */

namespace AgenturKonitzer\RbAssetArchive;


use TYPO3\CMS\Form\Domain\Model\Element\ImagebuttonElement;
use TYPO3\CMS\Extbase\Configuration as ExtbaseConfiguration;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class RbAssetArchiveHooks {

    const BASIC_PATH = 'uploads/tx_rbassetarchive/';

    /* Namens-Suffixe f. Bildformate */
    const LARGE = 'large';
    const LARGE540 = 'large_width540';
    const SMALL = 'small';
    const THUMB_LARGE = 'thumb_l';
    const THUMB_SMALL = 'thumb_s';

    private $allFormats = array(self::LARGE => 'widthLarge300dpi',
                                self::LARGE540 => 'widthLargeWeb',
                                self::SMALL => 'widthSmallWeb',
                                self::THUMB_LARGE => 'widthThumbLarge',
                                self::THUMB_SMALL => 'widthThumbSmall');

    /**
     * First Test
     *
     * @param string $status status
     * @param string $table table name
     * @param integer $recordUid id of the record
     * @param array $fields fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject parent Object
     * @return void
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $recordUid, array $fields, \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject) {
            
            
        // Nur durchführen für Rolf Benz Asset Archive-Datensätze
        if ($table == 'tx_rbassetarchive_domain_model_asset') {
//            $fileName = $_SERVER['DOCUMENT_ROOT'].'/'.self::BASIC_PATH.'test.txt';
//            $textfile = new \SplFileObject($fileName, 'w');

            /* Prüfen, ob Zielverzeichnisse bereits existieren;
              wenn nicht, anlegen
            */
            $basicPath = $_SERVER['DOCUMENT_ROOT'].'/'.self::BASIC_PATH.'/';
            foreach (array_keys($this->allFormats) as $format) {
                if (!is_dir($basicPath.$format)) {
                    mkdir($basicPath.$format, 0755);
                }
            }

            // TS-Parameter holen
            $config = self::getTsSetup('tx_rbassetarchive');

            // Prüfen, ob Bild und Thumbnail im typo-Datensatz gespeichert ist
            if (array_key_exists('image', $fields)) {
                $imageName = $fields['image'];
            } else {
                $imageName = $parentObject->checkValue_currentRecord['image'];
            }

            //replace name if it is zip
            $imageName = preg_replace('"\.zip$"', '_q.tif', $imageName); 


            // detect original file name
            $fileInfo = \TYPO3\CMS\Core\Utility\GeneralUtility::split_fileref($imageName);
            $orgFileName = preg_replace('/_[0-9][0-9]$/', '', $fileInfo['filebody']);
            $newBasicFileName = $orgFileName.'_';
var_dump(is_null($fields['thumb']));
            /* Rechteckige Original verarbeiten */
            if (array_key_exists('thumb', $fields) && !empty($fields['thumb']) && !($fields['thumb'] == '') && !(is_null( $fields['thumb']))) {
                $thumbName = $fields['thumb'];
            } else {
                // fetch thumb name from field image name (convention over configuration)
              $thumbName = $fileInfo['path'].preg_replace('"\_q_$"', '_',$newBasicFileName).'q.'.strtolower($fileInfo['fileext']);
                
                 var_dump($thumbName);
                if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$thumbName)) {
                    $thumbName = $fileInfo['path'].preg_replace('"\_q_$"', '_',$newBasicFileName).'q.'.strtoupper($fileInfo['fileext']);
                    var_dump($thumbName);
                       if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$thumbName)) {
                        unset($thumbName);
                    }
                }
            }

            $file = new \Imagick();

            foreach ($this->allFormats as $format => $setting) {
                $imageProcessing = false;

                $width = $config['settings'][$setting];
                // Rechteckiges Format
                if (strpos($format, 'thumb') === false) {
                    if (!empty($imageName)) {
                        $imageProcessing = $file->readimage($_SERVER['DOCUMENT_ROOT'].'/'.$imageName);
                    }
                    $d = $file->getimagegeometry();
                    //var_dump($d);
                    $height = $d['height']/$d['width']*$width;
                } else { // Quadratisches Format
                    if (!empty($thumbName)) {
                        $imageProcessing = $file->readimage($_SERVER['DOCUMENT_ROOT'].'/'.$thumbName);
                    }
                    $width = 574;
                    $height = $width;
                }

                if ($imageProcessing) {
                    /**
                     * Farb-Profil setzen, um "ausbleichen" zu verhindern
                     * Weitere verfügbare Farb-Profile (Quelle: http://www.color.org):
                     *   - sRGB_v4_ICC_preference.icc
                     *   - sRGB_IEC61966-2-1_black_scaled.icc
                     */
                    $icc_rgb = file_get_contents(ExtensionManagementUtility::extPath('rb_asset_archive').'Resources/Private/Misc/sRGB_IEC61966-2-1_no_black_scaling.icc');
                    $file->profileimage('icc', $icc_rgb);
                    // Image-Profil löschen, sonst kann die Auflösung nicht verändert werden
                    $file->stripimage();
                    // ausser bei Format 'large' Auflösung auf 72 dpi setzen (derzeitiger Default-Wert)
                    if (strpos($setting, 'Web') !== false || strpos($setting, 'Thumb') !== false) {
                        @$file->setResolution($config['settings']['webDpi'], $config['settings']['webDpi']);
                        @$file->resampleImage($config['settings']['webDpi'],$config['settings']['webDpi'], \imagick::FILTER_UNDEFINED,0.5);
                    }

//                    $file->setcolorspace(\imagick::COLORSPACE_RGB);
                    @$file->setImageFormat('jpg');
                    @$file->resizeimage($width, $height, \imagick::FILTER_CATROM, 0.9);
                    @$file->writeimage($basicPath.$format.'/'.preg_replace('"\_q_$"', '_', $newBasicFileName).$format.'.jpg'); 
                    //var_dump(($basicPath.$format.'/'.preg_replace('"\_q_$"', '_', $newBasicFileName).$format.'.jpg'));
                }
            }

            $fields_values = array('basic_file_name' => $orgFileName);
            if (file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$thumbName)) {
                $fields_values['thumb'] = $thumbName;
            }
            $realUid = $parentObject->substNEWwithIDs[$recordUid];
            var_dump($recordUid);
            $this->saveInformation($table, $recordUid, $fields_values);

//            /* Zusätzlich Quadratische Original verarbeiten */
//            $QorgFileName = $orgFileName.'_q';
//            $QnewBasicFileName = $QorgFileName.'_';
//            $QthumbName = $fileInfo['path'].$QnewBasicFileName.'square.'.$fileInfo['fileext'];
        }
    }

    private function saveBasicFileName($table, $recordUid, $basicFileName) {
        $fields_values = array('basic_file_name' => $basicFileName); 
        $this->saveInformation($table, $recordUid, $fields_values);
    }

    private function saveInformation($table, $recordUid, $fields_values)
    {
        $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
            $table,
            'uid='.intval($recordUid),
            $fields_values
        );
    }

    /**
     * Gibt fuer $_EXTKEY das 'plugin.'-Array (ohne Punkte) zurueck
     *
     * @param string Extension-Key z.B.'tx_extbase'
     * @param boolean true für plugin TS, falls für config TS
     * @return array
     */
    private static function getTsSetup($_EXTKEY, $plugin = true) {
        $cm = new ExtbaseConfiguration\BackendConfigurationManager();
        $settings = $cm->getTypoScriptSetup();
        // TypoScript von $_EXTKEY laden
//        $settings = t3lib_div::makeInstance('ExtbaseConfiguration\BackendConfigurationManager')->getTypoScriptSetup();
        return self::removeDots($settings[$plugin ? 'plugin.' : 'config.'][$_EXTKEY.'.']);
    }

    /**
     * Entfernt Punkte in dem TypoScript-Array
     *
     * @return void
     */
    private static function removeDots($settings) {
        $conf = array();
        foreach ($settings as $key => $value)
            $conf[self::removeDotAtTheEnd($key)] = is_array($value) ? self::removeDots($value) : $value;
        return $conf;
    }

    /**
     * Entfernt einen Punkt am ende von $string
     *
     * @param string $string
     * @return string $string
     */
    private static function removeDotAtTheEnd($string) {
        return preg_replace('/\.$/', '', $string);
    }

}


?>