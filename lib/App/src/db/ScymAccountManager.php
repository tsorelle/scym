<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 12:21 PM
 */

namespace App\db;


use App\db\api\FeeTypeDto;
use App\db\api\IAttenderCostInfo;
use App\db\api\RegistrationAccount;
use App\db\scym\ScymAnnualSession;
use App\db\scym\ScymCharge;
use App\db\scym\ScymCredit;
use App\db\scym\ScymDonation;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;

class ScymAccountManager
{
    private $payments = array();
    private $credits = array();
    private $charges = array();
    private $donations = array();
    private $aidEligiableTotal = 0.00;
    private $aidRequestedTotal = 0.00;

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

    const DONATION_ID_YM = 2;
    const DONATION_ID_SIMPLEMEAL = 3;


    /**
     * @var ScymRegistrationsManager
     */
    private $registrationsManager;

    /**
     * @var FeeTypeDto[]
     */
    private $fees;
    private $housingTypes;
    private $creditTypes;
    private $registrationId = 0;


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

    /**
     * @param $feeCode
     * @return FeeTypeDto
     * @throws \Exception
     */
    private function getFeeType($feeCode)
    {
        if (array_key_exists($feeCode,$this->fees)) {
            return $this->fees[$feeCode];
        }
        throw new \Exception("Fee code '$feeCode' not found.");
    }
    private function createRegistrationItems($year,\DateTime $receivedDate,$ymDonation, $simpleMealDonation, $aidRequested)
    {
        $sessionInfo = $this->registrationsManager->getSession($year);
        if ($receivedDate > $sessionInfo->getDeadline()) {
            $lateFee = $this->getFeeType('LATE');
            $basis = "Late registration received ".$receivedDate->format('m/d/Y').'.';
            $this->addCharge($lateFee->unitAmount,$lateFee->feeTypeId,$$basis);
        }
        if (!empty($ymDonation)) {
            $this->addDonation($ymDonation,self::DONATION_ID_YM);
        }
        if (!empty($simpleMealDonation)) {
            $this->addDonation($ymDonation,self::DONATION_ID_SIMPLEMEAL);
        }
    }

    private function createAttenderItems(IAttenderCostInfo $attender)
    {

    }


    private function addCredit($amount,$creditTypeId,$description) {
        $credit = ScymCredit::newCredit($amount,$description,$creditTypeId);
        if ($this->registrationId) {
            $credit->setRegistrationid($this->registrationId);
        }
        $this->
        array_push($this->credits,$credit);
    }

    private function addCharge($amount,$feeTypeId,$basis,$canWaive = true) {
        $charge = ScymCharge::newCharge($amount,$basis,$feeTypeId);
        if ($this->registrationId) {
            $charge->setRegistrationid($this->registrationId);
        }
        array_push($this->charges,$charge);
        if (!$canWaive) {
            $this->aidEligiableTotal += $amount;
        }
    }

    private function addDonation($amount,$donationTypeId) {
        $donation = ScymDonation::createDonation($donationTypeId,$amount);
        if ($this->registrationId) {
            $donation->setRegistrationid($this->registrationId);
        }
        array_push($this->donations,$donation);
    }

    private function doCalculations($year=null, \DateTime $recievedDate=null, array $attenders, $ymDonation = null, $simpleMealDonation = null, $aidRequested = null)
    {
        $this->clear();
        $this->createRegistrationItems($year, $recievedDate, $ymDonation, $simpleMealDonation, $aidRequested);
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
        $today = new \DateTime();
        return $this->doCalculations(null,$today,$attenders, $ymDonation, $simpleMealDonation, $aidRequested);
    }

    public function recalculateForRegistration($registrationId)
    {
        $registration = $this->registrationsManager->getRegistration($registrationId);
        return $this->recalculate($registration);
    }

    public function recalculate(ScymRegistration $registration)
    {
        $this->clear();
        $this->registrationId = $registration->getRegistrationId();
        // todo: clear existing items
        $summary = $this->doCalculations(
            $registration->getYear(),
            $registration->getReceivedDate(),
            $registration->getAttenders(),
            $registration->getYMDonation(),
            $registration->getSimpleMealDonation(),
            $registration->getFinancialAidRequested());
        // todo: add new items
        return $summary;
    }

}