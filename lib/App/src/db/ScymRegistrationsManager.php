<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/27/2015
 * Time: 8:35 AM
 */

namespace App\db;

use App\db\api\CreditTypeDto;
use App\db\api\HousingTypeDto;
use App\db\scym\api\IAttender;
use App\db\scym\ScymAnnualSession;
use App\db\scym\ScymAttender;
use App\db\scym\ScymCreditType;
use App\db\scym\ScymDonationType;
use App\db\scym\ScymFee;
use App\db\scym\ScymHousingType;
use App\db\scym\ScymRegistration;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Tops\db\TDbServiceManager;
use Tops\sys\TListItem;

class ScymRegistrationsManager extends TDbServiceManager
{
    /**
     * @param null $year
     * @return null|ScymAnnualSession
     */
    public function getSession($year = null) {
        $repository =  $this->getRepository('App\db\scym\ScymAnnualSession');
        /**
         * @var $result ScymAnnualSession
         */
        $result=null;
        if (empty($year)) {
            $year = date("Y"); // current year
            $result = $repository->find($year);
            if (empty($result)) {
                throw new Exception("Yearly meeting year $year is not in the annualsessions table.");
            }
            // if we are 60 days past the yearly meeting end date for the curren year, go to next year.
            $endDate = clone $result->getEnd(); // must clone because $endDate->add would change $result->end
            $today = new \DateTime();
            if ($today > $endDate->add(new \DateInterval('P60D'))) {
                $year++;
                $result = null; // try again
            }
        }
        if ($result == null) {
            $result = $repository->find($year);
            if (empty($result)) {
                throw new Exception("Yearly meeting year $year is not in the annualsessions table.");
            }
        }
        return $result;
    }

    public function getUserRegistrationId($username, $year) {
        $repository =  $this->getRepository('App\db\scym\ScymRegistration');
        $result = $repository->findOneBy(array('username' => $username, 'year' => $year));
        if ($result) {
            return $result->getRegistrationId();
        }
        return 0;
    }

    public function getFeeTables() {
        $repository =  $this->getRepository('App\db\scym\ScymFee');
        $fees = $repository->findAll();
        $result = array();
        foreach ($fees as $fee) {
            /**
             * @var $fee ScymFee
             */
            $item = $fee->getDataTransferObject();
            $result[$item->feeCode] = $item;
        }
        return $result;
    }

    public function getNonWaivableFees() {
        $em = $this->getEntityManager();
        $q = $em->createQuery("select f from App\db\scym\ScymFee f where f.canwaive = 0");
        $fees = $q->getResult();
        $result = array();
        foreach ($fees as $fee) {
            /**
             * @var $fee ScymFee
             */
            array_push($result,$fee->getFeeTypeId());
        }
        return $result;
    }

    public function getFeeDescriptions() {
        $repository =  $this->getRepository('App\db\scym\ScymFee');
        $fees = $repository->findAll();
        $result = array();
        foreach ($fees as $fee) {
            /**
             * @var $fee ScymFee
             */
            $result[$fee->getFeeTypeId()] = $fee->getDescription();
        }
        return $result;
    }

    /**
     * @return CreditTypeDto[]
     */
    public function getCreditTypes() {
        $repository =  $this->getRepository('App\db\scym\ScymCreditType');
        $types = $repository->findBy(array('active' => 1));
        $result = array();
        foreach ($types as $type) {
            /**
             * @var $type ScymCreditType
             */
            $item = $type->getDataTransferObject();
            $id = $type->getCreditTypeid();
            $result[$id] = $item;
        }
        return $result;

    }

    /**
     * @return HousingTypeDto[]
     */
    public function getHousingTypes() {
        $repository =  $this->getRepository('App\db\scym\ScymHousingType');
        $types = $repository->findBy(array('active' => 1));
        $result = array();
        foreach ($types as $type) {
            /**
             * @var $type ScymHousingType
             */
            $item = $type->getDataTransferObject();
            $result[$item->housingTypeId] = $item;
        }
        return $result;
    }

    /**
     * @param $id
     * @return ScymRegistration
     * @throws \Exception
     */
    public function getRegistration($id) {
        $repository =  $this->getRepository('App\db\scym\ScymRegistration');
        $result = $repository->find($id);
        if ($result == null) {
            throw new \Exception("Cannot find registration #$id");
        }
        return $result;
    }

    /**
     * @param $registrationCode
     * @return ScymRegistration
     */
    public function getRegistrationByCode($registrationCode) {
        $repository =  $this->getRepository('App\db\scym\ScymRegistration');
        $result = $repository->findOneBy(array('registrationCode' => $registrationCode));
        return $result;
    }

    public function checkRegistationCodeExists($registrationCode) {
        $em = $this->getEntityManager();
        $q = $em->createQuery('select r.registrationCode from App\db\scym\ScymRegistration r where r.registrationCode = ?1');
        $q->setParameter(1,$registrationCode);
        $result = $q->getResult();
        return (!empty($result));
    }

    public function getFundList() {
        $repository =  $this->getRepository('App\db\scym\ScymDonationType');
        $types = $repository->findBy(array('registrationform' => 1));
        $result = array();
        foreach ($types as $type) {
            /**
             * @var $type ScymDonationType
             */
            $item = $type->toLookupItem();
            array_push($result, $item);
        }
        return $result;
    }

    public function deleteAccountItems(ScymRegistration $registration) {
        $em = $this->getEntityManager();
        $items = $registration->removeAccountItems();
        foreach ($items as $item) {
            $em->remove($item);
        }
    }

    public function deleteAttender(ScymRegistration $registration, $attenderId) {
        $em = $this->getEntityManager();
        $attender = $registration->removeAttenderById($attenderId);
        $em->remove($attenderId);
    }

    public function deleteRegistration(ScymRegistration $registration) {
        $em = $this->getEntityManager();
        $em->remove($registration);
        $em->flush();
    }

    public function deleteAttenders(ScymRegistration $registration, array $attenderIds) {
        if (!empty($attenderIds)) {
            $em = $this->getEntityManager();
            foreach ($attenderIds as $attenderId) {
                $attender = $registration->removeAttenderById($attenderId);
                if ($attender) {
                    $em->remove($attender);
                }
            }
        }
    }

    public function clearAccountItems(ScymRegistration $registration) {
        $em = $this->getEntityManager();
        $items = $registration->removeAccountItems();
        foreach ($items as $item) {
            $em->remove($item);
        }
    }

    public function updateMeals(ScymAttender $attender, array $mealtimes)
    {
        $em = $this->getEntityManager();
        $removed = $attender->updateMeals($mealtimes);
        foreach ($removed as $meal) {
            $em->remove($meal);
        }
    }
}