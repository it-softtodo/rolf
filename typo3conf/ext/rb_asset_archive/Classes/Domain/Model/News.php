<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 31.08.13
 * Zeit: 16:12
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
class News extends \Tx_News_Domain_Model_News {

    /**
     * the asstes
     *
     * @var \Tx_Extbase_Persistence_ObjectStorage<\AgenturKonitzer\RbAssetArchive\Domain\Model\Asset>
     */
    protected $assets;

    /**
     * the news_id
     *
     * @var string
     */
    protected $newsId;

    /**
     * contact
     *
     * @var \AgenturKonitzer\RbAssetArchive\Domain\Model\Address
     */
    protected $contact;

    /**
     * countries
     *
     * @var \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category>
     */
    protected $countries;

    /**
     * brand
     *
     * @var \Tx_News_Domain_Model_Category
     */
    protected $brand;

    /**
     * programms
     *
     * @var \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category>
     */
    protected $programms;

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
        $this->assets = new \Tx_Extbase_Persistence_ObjectStorage();
        $this->countries = new \Tx_Extbase_Persistence_ObjectStorage();
        $this->programms = new \Tx_Extbase_Persistence_ObjectStorage();
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
     * @param string $newsId
     */
    public function setNewsId($newsId) {
        $this->newsId = $newsId;
    }

    /**
     * @return string
     */
    public function getNewsId() {
        return $this->newsId;
    }

    /**
     * @param \AgenturKonitzer\RbAssetArchive\Domain\Model\Address $contact
     */
    public function setContact($contact) {
        $this->contact = $contact;
    }

    /**
     * @return \AgenturKonitzer\RbAssetArchive\Domain\Model\Address
     */
    public function getContact() {
        return $this->contact;
    }

    /**
     * Returns the countries
     *
     * @return \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category> $countries
     */
    public function getCountries() {
        return $this->countries;
    }

    /**
     * Sets the countries
     *
     * @param \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category> $countries
     * @return void
     */
    public function setCountries(\Tx_Extbase_Persistence_ObjectStorage $countries) {
        $this->countries = $countries;
    }

    /**
     * @return \AgenturKonitzer\RbAssetArchive\Domain\Model\Tx_News_Domain_Model_Category
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @param \AgenturKonitzer\RbAssetArchive\Domain\Model\Tx_News_Domain_Model_Category $brand
     */
    public function setBrand($brand) {
        $this->brand = $brand;
    }

    /**
     * @return \AgenturKonitzer\RbAssetArchive\Domain\Model\Tx_News_Domain_Model_Category
     */
    public function getBrand() {
        return $this->brand;
    }

    /**
     * Returns the programms
     *
     * @return \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category> $programms
     */
    public function getProgramms() {
        return $this->programms;
    }

    /**
     * Sets the programms
     *
     * @param \Tx_Extbase_Persistence_ObjectStorage<\Tx_News_Domain_Model_Category> $programms
     * @return void
     */
    public function setProgramms(\Tx_Extbase_Persistence_ObjectStorage $programms) {
        $this->programms = $programms;
    }


}