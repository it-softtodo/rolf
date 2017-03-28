<?php

namespace AgenturKonitzer\RbAssetArchive\Tests;
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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Rolf Benz // Bild-Archiv
 *
 * @author Martin Lazar-Rudolf <martin@lazar-rudolf.de>
 */
class AssetTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {
	/**
	 * @var \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new \AgenturKonitzer\RbAssetArchive\Domain\Model\Asset();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setNameForStringSetsName() { 
		$this->fixture->setName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getName()
		);
	}
	
	/**
	 * @test
	 */
	public function getActiveReturnsInitialValueForOolean() { }

	/**
	 * @test
	 */
	public function setActiveForOoleanSetsActive() { }
	
	/**
	 * @test
	 */
	public function getImageReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setImageForStringSetsImage() { 
		$this->fixture->setImage('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getImage()
		);
	}
	
	/**
	 * @test
	 */
	public function getDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setDateForDateTimeSetsDate() { }
	
	/**
	 * @test
	 */
	public function getCountryReturnsInitialValueFor() { }

	/**
	 * @test
	 */
	public function setCountryForSetsCountry() { }
	
	/**
	 * @test
	 */
	public function getBrandReturnsInitialValueFor() { }

	/**
	 * @test
	 */
	public function setBrandForSetsBrand() { }
	
}
?>