<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/24/2016
 * Time: 5:16 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;
use Tops\sys\TEMailMessage;
use Tops\sys\TPostOffice;

class ConfirmRegistrationCommand extends TServiceCommand
{

    protected function run()
    {
        // todo: enable email in production
        $emailEnabled = false;

        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No service request.');
            return;
        }
        $registrationId = isset($request->registrationId) ? $request->registrationId : 0;
        if (empty($registrationId)) {
            $this->addErrorMessage('ERROR: No registration id.');
            return;
        }
        $messageText = isset($request->text) ? $request->text : null;
        if (empty($messageText)) {
            $this->addErrorMessage('ERROR: No message text in request.');
            return;
        }

        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage('ERROR: No registration found for id ' . $registrationId);
            return;
        }

        $email = $registration->getEmail();
        if (empty($email)) {
            $this->addErrorMessage('ERROR: No email found for registration.');
        }
        else if ($emailEnabled){
            /**
             * @var $message TEMailMessage
             */
            $message = TPostOffice::CreateMessageFromUs('registrar', 'SCYM Registration confirmed', $messageText);
            if ($message == null) {
                return; // in some unit test scenarios $message is not created. Ignore for these cases.
            }
            $message->addRecipient($email, $registration->getName());
            $message->addCC('registrations@scym.org');


            $sendResult = TPostOffice::Send($message);
            if ($sendResult === false) {
                $this->addInfoMessage('The mail service failed, so your confirmation was not sent.');
            }
        }
        $registration->setConfirmed(true);
        $manager->updateEntity($registration);
    }
}