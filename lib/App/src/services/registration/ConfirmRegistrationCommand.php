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
    public function __construct() {
        $this->addAuthorization("administer registrations");
    }

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
        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage('ERROR: No registration found for id ' . $registrationId);
            return;
        }


        $sendMessage = isset($request->text);

        if ($sendMessage) {
            if (empty($request->text)) {
                $this->addErrorMessage('No message text received.');
                return;
            }

            $email = $registration->getEmail();
            if (empty($email)) {
                $this->addErrorMessage('ERROR: No email found for registration.');
            } else if ($emailEnabled) {
                /**
                 * @var $message TEMailMessage
                 */
                $message = TPostOffice::CreateMessageFromUs('registrar', 'SCYM Registration confirmed', $request->text);
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
        }
        $registration->setConfirmed(true);
        $manager->updateEntity($registration);
        if (!$sendMessage) {
            $this->addInfoMessage('Housing assignments confirmed.');
        }
    }
}