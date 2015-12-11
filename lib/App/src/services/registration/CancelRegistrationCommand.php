<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/10/2015
 * Time: 5:19 PM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class CancelRegistrationCommand extends TServiceCommand
{

    protected function run()
    {
        $id = $this->getRequest();
        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($id);
        $manager->deleteRegistration($registration);
    }
}