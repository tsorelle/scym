<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/11/2016
 * Time: 9:01 AM
 */

namespace App\services\registration;

use App\db\scym\ScymCharge;
use App\db\scym\ScymCredit;
use App\db\scym\ScymDonation;
use App\db\scym\ScymPayment;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class UpdateAccountCommand extends TServiceCommand
{

    /**
     * @var ScymRegistrationsManager
     */
    private $manager;

    /***
     * Data contract:
     *      var request = {
     *          registrationId : me.registrationId(),
     *          itemType : itemType,
     *          data: data,
     *          action : action
     *          };
     */

    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage("ERROR: No request");
            return;
        }

        $registrationId = isset($request->registrationId) ? $request->registrationId : null;
        if (empty($registrationId)) {
            $this->addErrorMessage("ERROR: No registrationId");
            return;

        }
        $itemType = isset($request->itemType) ? $request->itemType : null;
        if (empty($itemType)) {
            $this->addErrorMessage("ERROR: No item type");
            return;
        }

        $itemData =        isset($request->data) ? $request->data : null;
        if (empty($itemData)) {
            $this->addErrorMessage("ERROR: No item data");
            return;
        }

        $this->manager = new ScymRegistrationsManager();

        $registration = $this->manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage("ERROR: No registration found for id #$registrationId");
            return;
        }
        $action = isset($request->action) ? $request->action : null;
        switch($action) {
            case 'add' :
                if (!$this->addItem($registration,$itemType,$itemData)) {
                    return;
                }
                break;
            case 'remove' :
                if (!$this->removeItem($registration,$itemType,$itemData))
                    return;
                break;
            default:
                $this->addErrorMessage("ERROR: missing or invalid action id '$action'");
                return;
        }

        $this->manager->saveChanges();
        $responseData = $this->manager->getAccountDetails($registrationId,false); // no lookups
        $this->setReturnValue($responseData);

    }

    private function addItem(ScymRegistration $registration, $itemType, $dto) {
        switch($itemType) {
            case 'payment' :
                $validPayment = ScymPayment::validatePayment($dto);
                if ($validPayment !== true) {
                    $this->addErrorMessage("Invalid payment: $validPayment");
                    return false;
                }
                $payment = ScymPayment::CreatePayment($dto);
                $registration->addPayment($payment);
                break;
            case 'credit' :
                $credit = ScymCredit::CreateCredit($dto);
                if (empty($credit)) {
                    $this->addErrorMessage("ERROR:Invalid or incomplete credit data.");
                    return false;
                }
                $registration->addCredit($credit);
                break;
            case 'charge' :
                $charge = ScymCharge::CreateCharge($dto);
                if (empty($charge)) {
                    $this->addErrorMessage("ERROR:Invalid or incomplete charge data.");
                    return false;
                }
                $registration->addcharge($charge);
                break;
            case 'donation' :
                $donation = ScymDonation::Create($dto);
                if (empty($dto)) {
                    $this->addErrorMessage("ERROR:Invalid or incomplete donation data.");
                    return false;
                }
                $registration->addDonation($donation);
                break;
            default:
                $this->addErrorMessage("Invalid item type '$itemType'");
                return false;
                break;
        }
        return true;
    }


    private function removeItem(ScymRegistration $registration, $itemType, $itemId) {
        switch($itemType) {
            case 'payment' :
                $entity = $registration->removePaymentById($itemId);
                break;
            case 'credit' :
                $entity = $registration->removeCreditById($itemId);
                break;
            case 'charge' :
                $entity = $registration->removeChargeById($itemId);
                break;
            case 'donation' :
                $entity = $registration->removeDonationById($itemId);
                break;
            default:
                $this->addErrorMessage("Invalid item type '$itemType'");
                return false;
        }
        if ($entity == null) {
            return false;
        }
        $this->manager->removeEntity($entity);
        return true;
    }

}