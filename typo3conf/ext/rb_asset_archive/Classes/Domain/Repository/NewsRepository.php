<?php
/**
 * presseportal
 *
 *
 * Erstellt: Martin Lazar-Rudolf (martin@lazar-rudolf.de)
 * Datum: 31.08.13
 * Zeit: 15:49
 * 
 */

namespace AgenturKonitzer\RbAssetArchive\Domain\Repository;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \AgenturKonitzer\RbAssetArchive\Controller\AssetController;


/**
 *
 *
 * @package rb_asset_archive
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class NewsRepository extends \Tx_News_Domain_Repository_NewsRepository {


    /**
     * @param array $arguments
     */
    public function filterNews($arguments) {
        // init
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);
        $and = array();
        $and[] = $query->greaterThan('uid', 0); // always true like 1=1

        // from filter
        if ($arguments['periodFromYear']) {
            $from = '01.'.$arguments['periodFromMonth'].'.'.$arguments['periodFromYear'];
            $and[] = $query->greaterThanOrEqual('datetime', new \DateTime($from));
        }

        // to filter
        if ($arguments['periodToYear']) {
            $to = '31.'.$arguments['periodToMonth'].'.'.$arguments['periodToYear'];
            $and[] = $query->lessThanOrEqual('datetime', new \DateTime($to));
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
            $or = array();
            $or[] = $query->like('title', '%'.$arguments['keyword'].'%');
            $or[] = $query->like('teaser', '%'.$arguments['keyword'].'%');
            $or[] = $query->like('bodytext', '%'.$arguments['keyword'].'%');

            $and[] = $query->logicalOr($or);
        }

        // country filter
        if ($arguments['country']) {
            $and[] = $query->contains('countries', $arguments['country']);
        }

        // brand filter
        if ($arguments['brands']) {
            $or = array();
            foreach ($arguments['brands'] as $brand) {
                $or[] = $query->equals('brand', $brand);
            }
            $and[] = $query->logicalOr($or);
        }



        $constraint = $query->logicalAnd($and); // create where object with AND
        $query->matching($constraint); // use constraint
        $query->setOrderings(array('datetime' => \Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));

        return $query->execute();
    }


    /**
     * @param int $limit
     */
    public function findLatest($limit, $countryId, $brands) {
        
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);

        $and = array();
        $and[] = $query->greaterThan('uid', 0);

        // brand filter
        if ($brands) {
            $or = array();
            foreach ($brands as $brand) {
                $or[] = $query->equals('brand', $brand);
            }
            $and[] = $query->logicalOr($or);
        }

        if ($countryId != 0) {
            $and[] = $query->contains('countries', $countryId);
        }

        $constraint = $query->logicalAnd($and); // create where object with AND
        $query->matching($constraint);

        $query->setOrderings(array('datetime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
        $query->setLimit($limit);
        return $query->execute();
    }

    /**
     * @param array  $arguments
     */
    public function findNewsByNewsId($arguments) {

        $newsId = $arguments['newsId'];

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);

        $and = array();
        $and[] = $query->equals('newsId', $newsId);

        // brand filter
        if ($arguments['brands']) {
            $or = array();
            foreach ($arguments['brands'] as $brand) {
                $or[] = $query->equals('brand', $brand);
            }
            $and[] = $query->logicalOr($or);
        }

        // länder filter
        if ($arguments['country']) {
            $and[] = $query->contains('countries', $arguments['country']);
        }

        $constraint = $query->logicalAnd($and);

        $query->matching($constraint);

        $result = $query->execute();
        return $result[0];
    }

    /**
     * @param array  $arguments
     */
    public function findBySearchText($arguments) {

        $searchText = $arguments['searchText'];

        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(FALSE);
        $query->getQuerySettings()->setRespectSysLanguage(FALSE);


        $and = array();

        $or = array();
        $or[] = $query->like('title', '%'.$searchText.'%');
        $or[] = $query->like('teaser', '%'.$searchText.'%');
        $or[] = $query->like('bodytext', '%'.$searchText.'%');

        $and[] = $query->logicalOr($or); // create where object with OR

        // brand filter
        if ($arguments['brands']) {
            $or = array();
            foreach ($arguments['brands'] as $brand) {
                $or[] = $query->equals('brand', $brand);
            }
            $and[] = $query->logicalOr($or);
        }

        // länder filter
        if ($arguments['country']) {
            $and[] = $query->contains('countries', $arguments['country']);
        }

        $constraint = $query->logicalAnd($and);

        $query->matching($constraint);

        return $query->execute();
    }


    protected function createConstraintsFromDemand(\Tx_Extbase_Persistence_QueryInterface $query, \Tx_News_Domain_Model_DemandInterface $demand) {
        $constraints = array();

        if ($demand->getCategories() && $demand->getCategories() !== '0') {
            $constraints[] = $this->createCategoryConstraint(
                $query,
                $demand->getCategories(),
                $demand->getCategoryConjunction(),
                $demand->getIncludeSubCategories()
            );
        }

        // archived
        if ($demand->getArchiveRestriction() == 'archived') {
            $constraints[] = $query->logicalAnd(
                $query->lessThan('archive', $GLOBALS['EXEC_TIME']),
                $query->greaterThan('archive', 0)
            );
        } elseif ($demand->getArchiveRestriction() == 'active') {
            $constraints[] = $query->logicalOr(
                $query->greaterThanOrEqual('archive', $GLOBALS['EXEC_TIME']),
                $query->equals('archive', 0)
            );
        }

        // Time restriction greater than or equal
        if ($demand->getTimeRestriction()) {
            $timeLimit = 0;
            // integer = timestamp
            if (\Tx_News_Utility_Compatibility::canBeInterpretedAsInteger($demand->getTimeRestriction())) {
                $timeLimit = $GLOBALS['EXEC_TIME'] - $demand->getTimeRestriction();
            } else {
                // try to check strtotime
                $timeFromString = strtotime($demand->getTimeRestriction());

                if ($timeFromString) {
                    $timeLimit = $timeFromString;
                } else {
                    throw new \Exception('Time limit Low could not be resolved to an integer. Given was: ' . htmlspecialchars($timeLimit));
                }
            }

            $constraints[] = $query->greaterThanOrEqual(
                'datetime',
                $timeLimit
            );
        }

        // Time restriction less than or equal
        if ($demand->getTimeRestrictionHigh()) {
            $timeLimit = 0;
            // integer = timestamp
            if (\Tx_News_Utility_Compatibility::canBeInterpretedAsInteger($demand->getTimeRestrictionHigh())) {
                $timeLimit = $GLOBALS['EXEC_TIME'] + $demand->getTimeRestrictionHigh();
            } else {
                // try to check strtotime
                $timeFromString = strtotime($demand->getTimeRestrictionHigh());

                if ($timeFromString) {
                    $timeLimit = $timeFromString;
                } else {
                    throw new \Exception('Time limit High could not be resolved to an integer. Given was: ' . htmlspecialchars($timeLimit));
                }
            }

            $constraints[] = $query->lessThanOrEqual(
                'datetime',
                $timeLimit
            );
        }

        // top news
        if ($demand->getTopNewsRestriction() == 1) {
            $constraints[] = $query->equals('istopnews', 1);
        } elseif ($demand->getTopNewsRestriction() == 2) {
            $constraints[] = $query->equals('istopnews', 0);
        }

        // storage page
        if ($demand->getStoragePage() != 0) {
            $pidList = \t3lib_div::intExplode(',', $demand->getStoragePage(), TRUE);
            $constraints[] = $query->in('pid', $pidList);
        }

        // month & year OR year only
        if ($demand->getYear() > 0) {
            if (is_null($demand->getDateField())) {
                throw new \InvalidArgumentException('No Datefield is set, therefore no Datemenu is possible!');
            }
            if ($demand->getMonth() > 0) {
                if ($demand->getDay() > 0) {
                    $begin = mktime(0, 0, 0, $demand->getMonth(), $demand->getDay(), $demand->getYear());
                    $end = mktime(23, 59, 59, $demand->getMonth(), $demand->getDay(), $demand->getYear());
                } else {
                    $begin = mktime(0, 0, 0, $demand->getMonth(), 1, $demand->getYear());
                    $end = mktime(23, 59, 59, ($demand->getMonth() + 1), 0, $demand->getYear());
                }
            } else {
                $begin = mktime(0, 0, 0, 1, 1, $demand->getYear());
                $end = mktime(23, 59, 59, 12, 31, $demand->getYear());
            }
            $constraints[] = $query->logicalAnd(
                $query->greaterThanOrEqual($demand->getDateField(), $begin),
                $query->lessThanOrEqual($demand->getDateField(), $end)
            );
        }

        // Tags
        $tags = $demand->getTags();
        if ($tags) {
            $tagList = explode(',', $tags);

            foreach ($tagList as $singleTag) {
                $constraints[] =  $query->contains('tags', $singleTag);
            }
        }

//        // dummy records, used for UnitTests only!
//        if ($demand->getIsDummyRecord()) {
//            $constraints[] = $query->equals('isDummyRecord', 1);
//        }

        // Search
        if ($demand->getSearch() !== NULL) {
            /* @var $searchObject Tx_News_Domain_Model_Dto_Search */
            $searchObject = $demand->getSearch();

            $searchFields = \t3lib_div::trimExplode(',', $searchObject->getFields(), TRUE);
            $searchConstraints = array();

            if (count($searchFields) === 0) {
                throw new \UnexpectedValueException('No search fields defined', 1318497755);
            }

            $searchSubject = $searchObject->getSubject();
            foreach ($searchFields as $field) {
                if (!empty($searchSubject)) {
                    $searchConstraints[] = $query->like($field, '%' . $searchSubject . '%');
                }
            }

            if (count($searchConstraints)) {
                $constraints[] = $query->logicalOr($searchConstraints);
            }
        }

        // Regions
        if ($GLOBALS['TSFE']->sys_language_uid != 0) {
            $countryId = AssetController::getRegionBySyslanguageUid($GLOBALS['TSFE']->sys_language_uid);
            $constraints[] = $query->contains('countries', $countryId);
        }

        // Brands (Marken)
        $brands = AssetController::getBrands();
        if ($brands) {
            $or = array();
            foreach ($brands as $brand) {
                $or[] = $query->equals('brand', $brand);
            }
            $constraints[] = $query->logicalOr($or);
        }

        // Exclude already displayed
        if ($demand->getExcludeAlreadyDisplayedNews() && isset($GLOBALS['EXT']['news']['alreadyDisplayed']) && !empty($GLOBALS['EXT']['news']['alreadyDisplayed'])) {
            $constraints[] = $query->logicalNot(
                $query->in(
                    'uid',
                    $GLOBALS['EXT']['news']['alreadyDisplayed']
                )
            );
        }

        // Clean not used constraints
        foreach ($constraints as $key => $value) {
            if (is_null($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

}