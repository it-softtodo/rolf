<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 23.08.13
 * Zeit: 16:50
 * 
 */

class Tx_RbAssetArchive_TreeProvider_DatabaseTreeDataProvider extends Tx_News_TreeProvider_DatabaseTreeDataProvider {


    /**
     * Required constructor
     *
     * @param array $tcaConfiguration
     * @param string $table
     * @param string $field
     * @param array $currentValue
     */
    public function __construct (array $tcaConfiguration, $table, $field, array $currentValue) {
        preg_match('/###REC_FIELD_(.*)###/', $tcaConfiguration['treeConfig']['dependedRootUid'], $match);
        $this->setRootUid($currentValue[$match[1]]);
    }

}

?>