<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/27/2015
 * Time: 8:35 AM
 */

namespace App\db;

use App\db\scym\ScymAnnualSession;
use App\db\scym\ScymDonationType;
use App\db\scym\ScymFee;
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

    public function getCreditTypes() {
        $repository =  $this->getRepository('App\db\scym\ScymCreditType');
        $types = $repository->findBy(array('active' => 1));
        $result = array();
        foreach ($types as $type) {
            $item = $type->getDataTransferObject();
            $result[$item->creditTypeCode] = $item;
        }
        return $result;

    }

    public function getHousingTypes() {
        $repository =  $this->getRepository('App\db\scym\ScymHousingType');
        $types = $repository->findBy(array('active' => 1));
        $result = array();
        foreach ($types as $type) {
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
}