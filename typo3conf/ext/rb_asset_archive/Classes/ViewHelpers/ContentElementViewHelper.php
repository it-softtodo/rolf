<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 12.03.14
 * Zeit: 18:22
 * 
 */

/**
 * Shows Content Element
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_RbAssetArchive_ViewHelpers_ContentElementViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

    /**
     * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * Parse a content element
     *
     * @param	string		field current element
     * @return 	string		Parsed Content Element
     */
    public function render($field) {
        $cObj = $this->configurationManager->getContentObject();
        return $cObj->data[$field];
    }


}