<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 12:21 PM
 */

namespace App\src\db;


use App\db\api\IAttenderCostInfo;
use App\db\api\RegistrationAccount;
use App\db\scym\ScymAnnualSession;
use App\db\scym\ScymCharge;
use App\db\scym\ScymCredit;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;

class ScymAccountManager
{
    private $payments = array();
    private $credits = array();
    private $charges = array();
    private $donations = array();

    const SCYM_SUBSIDY = 2;
    const SIMPLE_MEAL = 3;

    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    const GENERATION_ADULT = 1;
    const GENERATION_YOUTH = 2; // 13-18
    const GENERATION_CHILD = 3; //4-12

    const HOUSINGFEE_TYPE_DORM = 1;
    const HOUSINGFEE_TYPE_FAM = 2;
    const HOUSINGFEE_TYPE_MOTEL = 3;

    const HOUSING_TYPE_NONE = 1;
    const HOUSING_TYPE_FAMILY = 6;
    const HOUSING_TYPE_MOTEL = 9;
    const HOUSING_TYPE_COUPLES = 8;
    const HOUSING_TYPE_HEALTH = 10;


    /**
     * @var ScymRegistrationsManager
     */
    private $registrationsManager;

    private $fees;
    private $housingTypes;
    private $creditTypes;
    private $registrationId = 0;
    /**
     * @var $receivedData DateTime
     */
    private $receivedDate = null;

    /**
     * @var $sessionInfo ScymAnnualSession
     */
    private $sessionInfo = null;

    public function __construct(ScymRegistrationsManager $manager = null)
    {
        $this->registrationsManager = $manager != null ? $manager : new ScymRegistrationsManager();
        $this->fees = $this->registrationsManager->getFeeTables();
        $sessionInfo = $this->registrationsManager->getSession();
        $this->housingTypes = $this->registrationsManager->getHousingTypes();
        $this->creditTypes = $this->registrationsManager->getCreditTypes();
        $this->deadline = $sessionInfo->getDeadline();
    }

    private function clear()
    {
        $this->payments = array();
        $this->credits = array();
        $this->charges = array();
        $this->donations = array();
    }

    private function createRegistrationItems($isLate,$ymDonation, $simpleMealDonation, $aidRequested)
    {

    }

    private function createAttenderItems(IAttenderCostInfo $attender)
    {

    }


    private function addCredit($amount,$creditTypeId,$description) {
        $credit = ScymCredit::newCredit($amount,$description,$creditTypeId);
        array_push($this->credits,$credit);
    }

    private function addCharge($amount,$basis,$feeTypeId) {
        $charge = ScymCharge::newCharge($amount,$basis,$feeTypeId);
        array_push($this->charges,$charge);
    }

    private function addDonation() {

    }

    private function doCalculations($attenders, $isLate = false, $ymDonation = null, $simpleMealDonation = null, $aidRequested = null)
    {
        $this->clear();

        $this->createRegistrationItems($isLate, $ymDonation, $simpleMealDonation, $aidRequested);
        foreach ($attenders as $attender) {
            $this->createAttenderItems($attender);
        }
        $result = new RegistrationAccount(
            $this->charges,
            $this->credits,
            $this->donations,
            $this->payments);
        return $result;
    }

    public function calculate($attenders, $ymDonation = null, $simpleMealDonation = null, $aidRequested = null)
    {
        $this->registrationId = 0;
        $this->sessionInfo = $this->registrationsManager->getSession(); // for current year
        return $this->doCalculations($attenders, $ymDonation, $simpleMealDonation, $aidRequested);
    }

    public function recalculateForRegistration($registrationId)
    {
        $registration = $this->registrationsManager->getRegistration($registrationId);
        $this->registrationId = $registration->getRegistrationId();
        return $this->recalculate($registration);
    }

    public function recalculate(ScymRegistration $registration)
    {
        $this->clear();
        $this->sessionInfo = $this->registrationsManager->getSession($registration->getYear());
        // todo: clear existing items
        $summary = $this->doCalculations($registration->getYMDonation(), $registration->getSimpleMealDonation(), $registration->getFinancialAidRequested());
        // todo: add new items
        return $summary;
    }

}