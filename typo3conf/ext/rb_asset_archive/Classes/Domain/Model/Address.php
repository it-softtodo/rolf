<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 02.09.13
 * Zeit: 11:21
 * 
 */

namespace AgenturKonitzer\RbAssetArchive\Domain\Model;


/**
 * Address model
 *
 * @package TYPO3
 * @subpackage rb_asset_archive
 */
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * gender
     *
     * @var string
     */
    protected $gender;

    /**
     * name
     *
     * @var string
     */
    protected  $name;

    /**
     * address
     *
     * @var string
     */
    protected $address;

    /**
     * country
     *
     * @var string
     */
    protected $country;

    /**
     * zip
     *
     * @var string
     */
    protected $zip;

    /**
     * city
     *
     * @var string
     */
    protected $city;

    /**
     * phone
     *
     * @var string
     */
    protected $phone;

    /**
     * fax
     *
     * @var string
     */
    protected $fax;

    /**
     * email
     *
     * @var string
     */
    protected $email;

    /**
     * @param string $gender
     */
    public function setGender($gender) {
        $this->gender = $gender;
    }

    /**
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param string $country
     */
    public function setCountry($country) {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }


    /**
     * @param string $zip
     */
    public function setZip($zip) {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     * @param string $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }


}