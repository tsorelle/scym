<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/19/2015
 * Time: 6:14 AM
 */

namespace App\services\registration;


use App\db\api\ICostItem;
use App\db\api\RegistrationAccount;
use App\db\scym\ScymDonation;
use App\db\scym\ScymPayment;
use App\db\ScymRegistrationsManager;
use Tops\sys\TDateTime;

class AccountService
{
    private $feeTypes;

    public function __construct(ScymRegistrationsManager $manager = null)
    {
        if ($manager == null) {
            $manager = new ScymRegistrationsManager();
        }
        $this->feeTypes = $manager->getFeeDescriptions();
    }

    /**
     * @param RegistrationAccount $account
     * @return \stdClass
     *   export interface IAccountSummary {
     *        fees : IListItem[];
     *        credits: IListItem[];
     *        donations: IListItem[];
     *        payments: IPaymentItem[];
     *        feeTotal: string;
     *        creditTotal: string;
     *        donationTotal: string;
     *        balance: any;
     *        aidEligibility: string;
     *    }
     */
    public function formatAccountSummary(RegistrationAccount $account) {
        $result = new \stdClass();
        $result->fees = $this->makeFeeList($account->getCharges());
        $result->credits = $this->makeCreditsList($account->getCredits());
        $result->donations = $this->makeDonationsList($account->getDonations());
        $result->payments = $this->makePaymentsList($account->getPayments());
        $result->feeTotal = $this->formatMoney($account->getChargesTotal());
        $result->creditTotal = $this->formatMoney($account->getCreditTotal());
        $result->donationTotal = $this->formatMoney($account->getDonationsTotal());
        $result->balance =  $this->formatMoney($account->getBalance());
        $result->aidEligibility = $this->formatMoney($account->getAidEligibility());
        return $result;
    }


    private function makeFeeList(array $charges) {
        $result = array();
        foreach($charges as $charge) {
            /** @var $charge \App\db\scym\ScymCharge  */
            $feeTypeId = $charge->getFeetypeid();
            $feeType = array_key_exists($feeTypeId,$this->feeTypes) ? $this->feeTypes[$feeTypeId] : '';
            $description = $charge->getBasis();
            $item = $this->makeLookupItem($charge,$feeType,$description);
            array_push($result,$item);
        }
        return $result;
    }

    private function makeCreditsList(array $credits)
    {
        $result = array();
        foreach($credits as $credit) {
            /** @var $credit \App\db\scym\ScymCredit  */
            $item = $this->makeLookupItem($credit, $credit->getDescription());
            array_push($result,$item);
        }
        return $result;

    }

    private function getDonationType(ScymDonation $donation) {
        switch($donation->getDonationtypeid()) {
            case 2 : return 'SCYM Subsidy';
            case 3 : return 'Simple Meal';
            default: return '';
        }
    }

    private function makeDonationsList(array $donations) {
        $result = array();
        foreach($donations as $donation) {
            /**
             * @var $donation ScymDonation
             */
            $item = $this->makeLookupItem($donation,$this->getDonationType($donation));
            $item->Key = $donation->getDonationtypeid();
            array_push($result,$item);
        }
        return $result;

    }

    private function makePaymentsList(array $payments) {
        $result = array();
        foreach($payments as $payment) {
            /** @var $payment \App\db\scym\ScymPayment  */
            $item = $this->makePaymentLookupItem($payment);
            array_push($result,$item);
        }
        return $result;
    }

    private function makeLookupItem(ICostItem $item, $text,$description='') {
        $result = new \stdClass();
        $result->Text = $text;
        $result->Description = $description;
        $result->Value = $this->formatMoney($item->getAmount());
        return $result;
    }

    private function makePaymentLookupItem(ScymPayment $payment)
    {
        $result = new \stdClass();
        $result->paymentId    = $payment->getPaymentid();
        $result->dateReceived = TDateTime::format($payment->getDateReceived(),'m/d/Y');
        $result->amount       = $this->formatMoney($payment->getAmount());
        $result->checkNumber  = $payment->getCheckNumber() ;
        $result->payor        = $payment->getPayor();
        return $result;
    }

    private function formatMoney($number) {
        $result = (empty($number) || !is_numeric($number)) ? '0.00' : number_format($number, 2, '.', ',');
        return '$'.$result;
    }

}