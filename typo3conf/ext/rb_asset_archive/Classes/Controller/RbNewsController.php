<?php
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

namespace AgenturKonitzer\RbAssetArchive\Controller;

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use \Tx_Flexslider_Domain_Model_FlexSlider;

class RbNewsController extends \Tx_Flexslider_Controller_FlexSliderController {

    /**
     * @var \Tx_News_Domain_Repository_NewsRepository
     */
    protected $newsRepository;

    /**
     * Inject a news repository to enable DI
     *
     * @param \Tx_News_Domain_Repository_NewsRepository $newsRepository
     * @return void
     */
    public function injectNewsRepository(\Tx_News_Domain_Repository_NewsRepository $newsRepository) {
        $this->newsRepository = $newsRepository;
    }

    /**
     * action teaser
     *
     * @return void
     */
    public function teaserAction() {
//        $GLOBALS['TSFE']->additionalHeaderData['rb.reference'] = '<link media="all" href="typo3conf/ext/flexslider/Resources/Public/Css/flexslider.css" type="text/css" rel="stylesheet">';

        if (!empty($_REQUEST['tx_news_pi1'])) {
            /**
             * @var \Tx_News_Domain_Model_News $news
             */
            $news = $this->newsRepository->findByUid($_REQUEST['tx_news_pi1']['news']);
            $headerImages = array();
            if ($news !== null) {
                $headerImages = $news->getMedia();
            }

            if (count($headerImages) > 0) {
                /** @var \Tx_Flexslider_Domain_Model_FlexSlider $slider */
                $flexSliders = array();
                /**
                 * @var \Tx_News_Domain_Model_Media $image
                 */
                foreach($headerImages as $image) {
                    /** @var \Tx_Flexslider_Domain_Model_FlexSlider $slider */
                    $slider = $this->objectManager->get('Tx_Flexslider_Domain_Model_FlexSlider');
                    $slider->setTitle($news->getTitle());
                    $slider->setCaption($news->getTitle());
                    $slider->setImage($image->getImage());
                    $flexSliders[] = $slider;
                }

                $tplObj = array(
                    'configuration' => \Tx_Flexslider_Utility_EmConfiguration::getConfiguration(),
                    'data' => $this->contentObject->data,
                    'altUid' => uniqid('alt'),
                    'flexSliders' => $flexSliders
                );

                $this->view->setTemplatePathAndFilename('typo3conf/ext/flexslider/Resources/Private/Templates/FlexSlider/List.html');
                $this->view->setPartialRootPath('typo3conf/ext/flexslider/Resources/Private/Partials');
                $this->view->assignMultiple($tplObj);
            }
        }
    }

} 