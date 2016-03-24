<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 3/23/2016
 * Time: 6:38 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class UpdateRegistrationNotesCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        if (empty($request)) {
            $this->$this->addErrorMessage('ERROR: No request recieved.');
            return;
        }

        if (!isset($request->registrationId)) {
            $this->$this->addErrorMessage('ERROR: No registrationId recieved.');
            return;

        }

        if (!isset($request->notes)) {
            $this->$this->addErrorMessage('ERROR: No notes text recieved.');
            return;
        }

        $action = isset($request->action) ? $request->action : 'registrarNotes';

        $manager = new ScymRegistrationsManager();
        $registration =  $manager->getRegistration($request->registrationId);
        if ($registration == null) {
            $this->addErrorMessage("Registration #$request->registrationId not found.");
            return;
        }

        if ($action == 'registrarNotes') {
            $registration->setScymNotes($request->notes);
        }
        else {
            $notes = $registration->getNotes();
            if (empty($notes)) {
                $notes = $request->notes;
            }
            else {
                $notes = "$notes\n\n$request->notes";
            }
            $registration->setNotes($notes);
        }
        $manager->saveChanges();
        if ($action == 'registrarNotes') {
            $this->addInfoMessage("Notes updated.");
        }

    }
}