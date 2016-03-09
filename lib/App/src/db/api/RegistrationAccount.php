<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/19/2015
 * Time: 6:17 AM
 */

namespace App\db\api;


use App\db\FeeTypeManager;
use App\db\scym\ScymCharge;
use App\db\scym\ScymCredit;
use App\db\scym\ScymDonation;
use App\db\scym\ScymPayment;


class RegistrationAccount
{
    public function __construct($charges,$credits,$donations, $aidEligibility = 0.00, array $payments = null)
    {
        $this->payments = ($payments == null) ? array() : $payments;
        $this->charges = ($charges == null) ? array() : $charges;
        $this->credits = ($credits == null) ? array() : $credits;
        $this->donations = ($donations == null) ? array() : $donations;
        $this->aidEligibility = $aidEligibility;
        $this->calculate();
    }

    /**
     * @var $nonWaivable int[]
     */
    private $nonWaivable = null;
    private $paymentTotal = 0.00;
    private $creditTotal = 0.00;
    private $chargesTotal = 0.00;
    private $donationsTotal = 0.00;
    private $balance = 0.00;
    private $aidEligibility = 0.00;

    public function calculate() {
        $this->paymentTotal = self::computeTotal($this->payments);
        $this->donationsTotal = self::computeTotal($this->donations);
        $this->creditTotal = self::computeTotal($this->credits);
        $this->chargesTotal = self::computeTotal($this->charges);
        $this->balance = ($this->chargesTotal + $this->donationsTotal) - ($this->paymentTotal + $this->creditTotal);
    }

    /**
     * @return ScymPayment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @return \App\db\scym\ScymCredit[]
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * @return ScymDonation[]
     */
    public function getDonations()
    {
        return $this->donations;
    }

    /**
     * @return \App\db\scym\ScymCharge[]
     */
    public function getCharges()
    {
        return $this->charges;
    }

    /**
     * @var ScymPayment[]
     */
    private $payments;
    /**
     * @var ScymCredit[]
     */
    private $credits;
    /**
     * @var ScymDonation[]
     */
    private $donations;
    /**
     * @var ScymCharge[]
     */
    private $charges;


    private static function computeTotal($items) {
        $result = 0.00;
        foreach($items as $item) {
            /**
             * @var $item ICostItem
             */
            $result += (float)$item->getAmount();
        }
        return $result;
    }

    public function getPaymentTotal()
    {
        return $this->paymentTotal;
    }

    public function getChargesTotal()
    {
        return $this->chargesTotal;

    }

    public function getDonationsTotal()
    {
        return $this->donationsTotal;
    }

    public function getCreditTotal()
    {
        return $this->creditTotal;
    }

    public function getBalance() {
        return $this->balance;
    }

    public function getAidEligibility() {
        return $this->aidEligibility;
    }

    public function getTotals() {
        $result = new \stdClass();
        $result->paymentTotal  = $this->paymentTotal;
        $result->chargesTotal  = $this->chargesTotal;
        $result->donationsTotal  = $this->donationsTotal;
        $result->creditTotal  = $this->creditTotal;
        $result->balance  = $this->balance;
        return $result;
    }
    
}