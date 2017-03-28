<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 13.03.14
 * Zeit: 12:28
 * 
 */

namespace AgenturKonitzer\RbAssetArchive\Domain\Model;

/**
 *
 *
 * @package rb_asset_archive
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class NewsContract extends \Tx_News_Domain_Model_News {


    /**
     * the asstes
     *
     * @var \Tx_Extbase_Persistence_ObjectStorage<\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset>
     */
    protected $assets;

    /**
     * @var string
     */
    protected $headline2ndline;

    /**
     * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects() {
        /**
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->assets = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds a Asset
     *
     * @param \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $category
     * @return void
     */
    public function addAsset(\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $asset) {
        $this->assets->attach($asset);
    }

    /**
     * Removes a Asset
     *
     * @param \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $assetToRemove The Asset to be removed
     * @return void
     */
    public function removeAsset(\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset $assetToRemove) {
        $this->assets->detach($assetToRemove);
    }

    /**
     * Returns the assets
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset> $assets
     */
    public function getAssets() {
        return $this->assets;
    }

    /**
     * Sets the assets
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset> assets
     * @return void
     */
    public function setAssets(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $assets) {
        $this->assets = $assets;
    }

    /**
     * Returns the assets as basic array, not as storage object
     *
     * @return array
     */
    public function getAssetArray() {
        return $this->assets->toArray();
    }

    /**
     * @param string $headline2line
     */
    public function setHeadline2ndline($headline2ndline)
    {
        $this->headline2ndline = $headline2ndline;
    }

    /**
     * @return string
     */
    public function getHeadline2ndline()
    {
        return $this->headline2ndline;
    }

}