<?php

namespace AgenturKonitzer\RbAssetArchive\Domain\Model;

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

/**
 *
 *
 * @package rb_asset_archive
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Asset extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * name
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $name;

    /**
     * name
     *
     * @var \string
     * @validate NotEmpty
     */
    protected $keywords;


	/**
	 * active
	 *
	 * @var boolean
	 */
	protected $active = FALSE;

	/**
	 * image
	 *
	 * @var \string
	 * @validate NotEmpty
	 */
	protected $image;

    /**
     * thumb
     *
     * @var \string
     * @validate NotEmpty
     */
    protected $thumb;

    /**
     * border
     *
     * @var boolean
     */
    protected $border = FALSE;

    /**
     * basic_file_name
     *
     * @var \string
     * @validate NotEmpty
     */
    protected $basicFileName;

	/**
	 * date
	 *
	 * @var \DateTime
	 * @validate NotEmpty
	 */
	protected $date;

	/**
	 * country
	 *
	 * @var Tx_News_Domain_Model_Category
	 */
	protected $country;

	/**
	 * brand
	 *
	 * @var Tx_News_Domain_Model_Category
	 */
	protected $brand;
	/**
	 * bildtyp
     *
	 * @var Tx_News_Domain_Model_Category
	 */
	protected $bildtyp ;

    /**
     * programm
     *
     * @var Tx_News_Domain_Model_Category
     */
    protected $programm;

    /**
     * @var \string
     */
    protected $thumbL;

    /**
     * @var \string
     */
    protected $large;

    /**
     * @var \string
     */
    protected $large540;


	/**
	 * Returns the name
	 *
	 * @return \string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param \string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getKeywords() {
        return $this->keywords;
    }

	/**
	 * Returns the active
	 *
	 * @return boolean $active
	 */
	public function getActive() {
		return $this->active;
	}

	/**
	 * Sets the active
	 *
	 * @param boolean $active
	 * @return void
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * Returns the boolean state of active
	 *
	 * @return boolean
	 */
	public function isActive() {
		return $this->getActive();
	}

	/**
	 * Returns the image
	 *
	 * @return \string $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param \string $image
	 * @return void
	 */
	public function setImage($image) {
		$this->image = $image;
	}

    /**
     * @param string $thumb
     */
    public function setThumb($thumb) {
        $this->thumb = $thumb;
    }

    /**
     * @return string
     */
    public function getThumb() {
        return $this->thumb;
    }

    /**
     * @param boolean $border
     */
    public function setBorder($border) {
        $this->border = $border;
    }

    /**
     * @return boolean
     */
    public function getBorder() {
        return $this->border;
    }

    /**
     * @param string $basicFileName
     */
    public function setBasicFileName($basicFileName) {
        $this->basicFileName = $basicFileName;
    }

    /**
     * @return string
     */
    public function getBasicFileName() {
        return $this->basicFileName;
    }



	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $date
	 * @return void
	 */
	public function setDate($date) {
		$this->date = $date;
	}

	/**
	 * Returns the country
	 *
	 * @return Tx_News_Domain_Model_Category $country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Sets the country
	 *
	 * @param Tx_News_Domain_Model_Category $country
	 * @return void
	 */
	public function setCountry(Tx_News_Domain_Model_Category $country) {
		$this->country = $country;
	}

	/**
	 * Returns the brand
	 *
	 * @return Tx_News_Domain_Model_Category $brand
	 */
	public function getBrand() {
		return $this->brand;
	}

	/**
	 * Sets the brand
	 *
	 * @param Tx_News_Domain_Model_Category $brand
	 * @return void
	 */
	public function setBrand(Tx_News_Domain_Model_Category $brand) {
		$this->brand = $brand;
	}

	/**
	 * Returns the bildtyp
	 *
	 * @return Tx_News_Domain_Model_Category $bildtyp
	 */
	public function getBildtyp() {
		return $this->bildtyp;
	}

	/**
	 * Sets the bildtyp
	 *
	 * @param Tx_News_Domain_Model_Category $bildtyp
	 * @return void
	 */
	public function setBildtyp(Tx_News_Domain_Model_Category $bildtyp) {
		$this->bildtyp = $bildtyp;
	}

    /**
     * Sets the programm
     *
     * @param Tx_News_Domain_Model_Category  $programm
     * @return void
     */
    public function setProgramm(Tx_News_Domain_Model_Category $programm) {
        $this->programm = $programm;
    }

    /**
     * Returns the programm
     *
     * @return Tx_News_Domain_Model_Category
     */
    public function getProgramm() {
        return $this->programm;
    }

    /**
     * @param mixed $thumbL
     */
    public function setThumbL($thumbL) {
        $this->thumbL = $thumbL;
    }

    /**
     * @return mixed
     */
    public function getThumbL() {
        return $this->thumbL;
    }

    /**
     * @param mixed $large
     */
    public function setLarge($large) {
        $this->large = $large;
    }

    /**
     * @return mixed
     */
    public function getLarge() {
        return $this->large;
    }

    /**
     * @param mixed $large540
     */
    public function setLarge540($large540) {
        $this->large540 = $large540;
    }

    /**
     * @return mixed
     */
    public function getLarge540() {
        return $this->large540;
    }

    public function getImageBaseName()
    {
        $path_parts = pathinfo($this->image);
        return $path_parts['filename'];
    }

}
?>