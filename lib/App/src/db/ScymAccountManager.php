<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 12:21 PM
 */

namespace App\db;


use App\db\api\CreditTypeDto;
use App\db\api\FeeTypeDto;
use App\db\api\HousingTypeDto;
use App\db\api\IAttenderCostInfo;
use App\db\api\IDonationInfo;
use App\db\api\RegistrationAccount;
use App\db\scym\ScymAnnualSession;
use App\db\scym\ScymCharge;
use App\db\scym\ScymCredit;
use App\db\scym\ScymDonation;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use Doctrine\Common\Collections\ArrayCollection;
use Tops\sys\TKeyValuePair;

class ScymAccountManager
{
    private $credits = array();
    private $charges = array();
    private $donations = array();
    private $payments = array();
    private $aidEligibleTotal = 0.00;
    private $aidAmountTotal = 0.00;

    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    const GENERATION_ADULT = 1;
    const GENERATION_YOUTH = 2; // 13-18
    const GENERATION_CHILD = 3; //4-12
    const GENERATION_INFANT = 5;

    const HOUSINGFEE_TYPE_DORM = 1;
    const HOUSINGFEE_TYPE_FAM = 2;
    const HOUSINGFEE_TYPE_MOTEL = 3;

    const HOUSING_TYPE_NONE = 1;
    const HOUSING_TYPE_FAMILY = 6;
    const HOUSING_TYPE_MOTEL = 9;
    const HOUSING_TYPE_COUPLES = 8;
    const HOUSING_TYPE_HEALTH = 10;

    const CREDIT_FINCIAL_AID = 1;



    /**
     * @var ScymRegistrationsManager
     */
    private $registrationsManager;

    /**
     * @var FeeTypeManager
     */
    private $fees;
    private $housingTypes;
    private $creditTypes;
    private $registrationId = 0;

    public function __construct(ScymRegistrationsManager $manager = null)
    {
        $this->registrationsManager = $manager != null ? $manager : new ScymRegistrationsManager();
        $fees = $this->registrationsManager->getFeeTables();
        $this->fees = new FeeTypeManager($fees);
        $sessionInfo = $this->registrationsManager->getSession();
        $this->housingTypes = $this->registrationsManager->getHousingTypes();
        $this->creditTypes = $this->registrationsManager->getCreditTypes();
        $this->deadline = $sessionInfo->getDeadline();
    }

    private function clear()
    {
        $this->credits = array();
        $this->charges = array();
        $this->donations = array();
        $this->aidEligibleTotal = 0.0;
    }

    /**
     * @param $typeId
     * @return CreditTypeDto
     * @throws \Exception
     */
    private function getCreditType($typeId) {
        if (array_key_exists($typeId,$this->creditTypes)) {
            return $this->creditTypes[$typeId];
        }
        throw new \Exception("Credit type #$typeId not found.");
    }

    /**
     * @param $typeId
     * @return HousingTypeDto
     * @throws \Exception
     */
    private function getHousingType($typeId) {
        if (array_key_exists($typeId,$this->housingTypes)) {
            return $this->housingTypes[$typeId];
        }
        throw new \Exception("Housing type #$typeId not found.");
    }

    private function applyLateFee($year,\DateTime $receivedDate)
    {
        $sessionInfo = $this->registrationsManager->getSession($year);
        if ($receivedDate > $sessionInfo->getDeadline()) {
            $lateFee = $this->fees->getFeeType('LATE');
            $basis = "Late registration received ".$receivedDate->format('m/d/Y').'.';
            $this->addCharge($lateFee->unitAmount,$lateFee->feeTypeId,$basis);
        }
    }

    private function getNights(IAttenderCostInfo $attender) {
        $arrivalDay = intval($attender->getArrivalTime() / 10);
        $departureDay = intval($attender->getDeparturetime() / 10);
        return $departureDay - $arrivalDay;
    }

    private function calcForDayVisitor(IAttenderCostInfo $attender) {
        $days = $this->getNights($attender) + 1;
        $feeCode = ($attender->getGenerationId() == self::GENERATION_ADULT) ? 'DAY' : 'YOUTHDAY';
        $this->addAttenderItem($attender,$feeCode,"$days days",$days);
        $meals = $attender->getMeals();
        if ($meals !== null) {
            $mealCount = count($meals);
            if (!empty($mealCount)) {
                $this->addAttenderItem($attender, 'MEAL', "$mealCount meals", $mealCount);
            }
        }
    }

    private function calcForAdult(IAttenderCostInfo $attender) {
        $this->addAttenderItem($attender,'REG');
        $housingType = $this->getHousingType($attender->getHousingTypeId());
        $nights = $this->getNights($attender);
        $category = $housingType->category == self::HOUSINGFEE_TYPE_DORM ? 'DORM' : 'ROOM';
        $housingFeeCode = 'ADULT'.$nights.'_'.$category;
        $note = $nights.' night'.($nights > 1 ? 's' : '');
        $this->addAttenderItem($attender, $housingFeeCode ,$note);
        if ($housingType->category == self::HOUSINGFEE_TYPE_MOTEL) {
            $specialNeeds = $attender->getSpecialNeedsTypeId();
            if ($housingType->housingTypeCode != 'HEALTH' || empty($specialNeeds)) {
                $this->addAttenderItem($attender, 'MOTEL_FEE',"Motel room");
                if ($attender->getSingleOccupant()) {
                    $this->addAttenderItem($attender, 'PRIVATE_ROOM_'.$nights, $note);
                }
            }
        }
    }

    private function calcForYouth(IAttenderCostInfo $attender) {
        $nights = $this->getNights($attender);
        $this->addAttenderItem($attender,'YOUTH'.$nights," $nights nights");
    }

    private function createAttenderItems(IAttenderCostInfo $attender)
    {
        $generation = $attender->getGenerationId();
        if ($generation == self::GENERATION_INFANT) {
            return;
        }
        $firstName = $attender->getFirstname();
        if ($attender->getLinens()) {
            $this->addAttenderItem($attender, 'LINEN');
        }
        $housingType = $attender->getHousingTypeId();
        if ($housingType == self::HOUSING_TYPE_NONE) {
            $this->calcForDayVisitor($attender);
        }
        else if ($generation != self::GENERATION_ADULT) {
            $this->calcForYouth($attender);
        }
        else { // adult
            $this->calcForAdult($attender);
        }
    }

    private function addCredit($amount,$creditTypeId,$description) {
        $credit = ScymCredit::newCredit($amount,$description,$creditTypeId);
        array_push($this->credits,$credit);
    }

    private function addAttenderItem(IAttenderCostInfo $attender, $feeCode, $note='', $count=1) {
        $fee = $this->fees->getFeeType($feeCode);
        $amount = $fee->unitAmount * $count;
        $description =  $attender->getFirstname();
        if ($note) {
            $description .= ' ' . $note;
        }

        $creditType = null;
        $aidTypeId =  $fee->canWaive ? $attender->getCreditTypeId() : 0;
        if ($aidTypeId) {
            $creditType = $this->getCreditType($aidTypeId);
            $description .= "(fees waived: $creditType->creditTypeName credit)";
        }

        $this->addCharge($amount,$fee->feeTypeId,$description,$fee->canWaive);
        if ($creditType) {
            $this->aidEligibleTotal -= $amount;
            $this->addCredit($amount, $aidTypeId, $creditType->description);
        }
    }

    private function addCharge($amount,$feeTypeId,$basis,$canWaive = true) {
        $charge = ScymCharge::newCharge($amount,$basis,$feeTypeId);
        array_push($this->charges,$charge);
        if ($canWaive) {
            $this->aidEligibleTotal += $amount;
        }
    }

    private function addDonation($amount,$donationTypeId) {
        $donation = ScymDonation::createDonation($donationTypeId,$amount);
        array_push($this->donations,$donation);
    }

    private function applyFinancialAid($aidAmount) {
        if ($aidAmount) {
            $amount = min($aidAmount, $this->aidEligibleTotal);
            if ($amount > 0) {
                $this->addCredit($amount, self::CREDIT_FINCIAL_AID, 'Financial aid');
            }
        }
    }

    private function createAccountObject()
    {
        $result = new RegistrationAccount(
            $this->charges,
            $this->credits,
            $this->donations,
            $this->aidEligibleTotal,
            $this->payments);
        return $result;
    }

    private function buildAccount($year, \DateTime $recievedDate, array $attenders, array $donations = array(), $aidAmount = 0.0, array $payments = array())
    {
        $this->clear();
        $this->payments = $payments;
        $this->donations = $donations;
        $this->applyLateFee($year, $recievedDate);
        foreach ($attenders as $attender) {
            $this->createAttenderItems($attender);
        }
        $this->applyFinancialAid($aidAmount);
        return $this->createAccountObject();
    }

    /**
     * @param array $attenders IAttenderCostItem[]
     * @param array $donations TKeyValuePair
     * @param float $aidAmount
     * @return RegistrationAccount
     */
    public function createAccount($recievedDate, array $attenders, array $donations = array(), $aidAmount = 0.0)
    {
        $this->payments = array();
        if ($recievedDate == null) {
            $recievedDate = new \DateTime();
        }
        $this->registrationId = 0;
        $result = $this->buildAccount(null,$recievedDate,$attenders,$donations,$aidAmount);
        return $result;
    }

    public function createAccountFromRegistration(ScymRegistration $registration) {
        $account = $this->createAccount(
            $registration->getReceivedDate(),
            $registration->getAttenders()->toArray(),
            $registration->getDonations()->toArray(),
            $registration->getFinancialAidAmount());
        $registration->addAccountItems($account);
        return $account;
    }

    public function getAccountFromRegistration(ScymRegistration $registration) {
        $this->payments = $registration->getPayments()->toArray();
        $this->charges = $registration->getCharges()->toArray();
        $this->credits = $registration->getCredits()->toArray();
        $this->donations = $registration->getDonations()->toArray();
        $this->registrationId = $registration->getRegistrationId();
        $this->aidEligibleTotal = 0.00;
        foreach ($this->charges as $charge) {
            /**
             * @var $charge ScymCharge
             */
            $fee = $this->fees->getFeeTypeById($charge->getFeetypeid());
            if ($fee->canWaive) {
                $this->aidEligibleTotal += (float)$charge->getAmount();
            }
        }

        return $this->createAccountObject();
    }

}