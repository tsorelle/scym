<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/23/2016
 * Time: 10:01 AM
 */

namespace App\services\registration;


use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;
use Tops\sys\TContentManager;
use Tops\sys\TTextTemplate;

class GetHousingConfirmationTextCommand extends TServiceCommand
{

    protected function run()
    {
        $registrationId = $this->getRequest();
        if ($registrationId == null) {
            $this->addErrorMessage("ERROR: No registration id received.");
            return;
        }

        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage("No registration found for id #$registrationId");
            return;
        }

        $accountManager = new ScymAccountManager($manager);
        $account = $accountManager->createAccountFromRegistration($registration);
        $balance = $account->getBalance();
        if ($balance > 0.00) {
            $balanceFormatted = (empty($balance) || !is_numeric($balance)) ? '0.00' : number_format($balance, 2, '.', ',');
            $cost = str_replace('{{balance}}',$balanceFormatted,
                'At this time, your fee for the event is ${{balance}}. Payment must be either check or cash as SCYM does not accept credit cards.');
        }
        else {
            $cost = 'No fees are due from you.';
        }

        $assignments = $manager->getHousingAssignmentsText($registrationId);
        if (empty($assignments)) {
            $this->addErrorMessage("No housing assignments were found.");
            return;
        }
        $assignmentsText = '';
        $attenderId = 0;
        foreach($assignments as $assignment) {
            if ($assignment->attenderId != $attenderId) {
                $assignmentsText .= "\n$assignment->firstName:\n";
                $attenderId = $assignment->attenderId;
            }
            $assignmentsText .= "    $assignment->day: $assignment->description\n";
        }

        // $session = $this->registrationsManager->getSession();
        $tokens = array(
            'room-assignments' => $assignmentsText,
            'code' => $registration->getRegistrationCode(),
            'cost' => $cost
        );
        $template = TContentManager::GetText('templates/housing-confirmation');
        $messageText = TTextTemplate::Merge($tokens, $template);
        $this->setReturnValue($messageText);
    }
}