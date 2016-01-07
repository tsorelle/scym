<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/7/2016
 * Time: 10:42 AM
 */

namespace App\services\registration;


use App\db\scym\ScymAttender;
use App\db\scym\ScymPayment;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class CheckInCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage("No request received.");
            return;
        }
        $attenders = isset($request->attenders) ? $request->attenders : null;
        $payment = isset($request->payment) ? $request->payment : null;
        $registrationId = isset($request->registrationId) ? $request->registrationId : null;

        if ($attenders == null && $payment == null) {
            return;
        }
        if ($registrationId == null) {
            $this->addErrorMessage("No registration id recieved.");
            return;
        }

        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);

        $changed = false;

        if ($payment != null && !empty($payment->amount)) {
            $validPayment = ScymPayment::validatePayment($payment);
            if (!empty($validPayment->errorMessage)) {
                $this->addErrorMessage("Invalid payment: $validPayment->errorMessage");
                return;
            }
            $paymentEntity = ScymPayment::CreatePayment($validPayment);
            $registration->addPayment($paymentEntity);
            $changed = true;
        }

        if ($attenders != null) {
            $attenderEntities = $registration->getAttenders();
            foreach($attenderEntities as $attenderEntity ) {
                /**
                 * @var $attenderEntity ScymAttender
                 */
                $attended = $this->getAttended($attenders,$attenderEntity->getAttenderId());
                if ($attended != $attenderEntity->getAttended()) {
                    $attenderEntity->setAttended($attended);
                    $changed = true;
                }
            }
        }
        if ($changed) {
            $manager->saveChanges();
            $this->addInfoMessage("Registration for '".$registration->getName()."' updated.");
        }
    }

    private function getAttended($attenders,$attenderId) {
        foreach($attenders as $attender) {
            if ($attender->attenderId == $attenderId && $attender->arrived) {
                return true;
            }
        }
        return false;
    }
}