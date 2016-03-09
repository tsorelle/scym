<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/2/2015
 * Time: 7:56 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class CheckRegistrationCodeCommand extends TServiceCommand
{

    protected function run()
    {
        $code = $this->getRequest();
        $manager = new ScymRegistrationsManager();
        $exists = $manager->checkRegistationCodeExists($code);
        $this->setReturnValue(!$exists);
    }
}