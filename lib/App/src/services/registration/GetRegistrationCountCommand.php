<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/7/2016
 * Time: 5:20 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetRegistrationCountCommand extends TServiceCommand
{

    protected function run()
    {
        $manager = new ScymRegistrationsManager();
        $counts = $manager->getRegistrationCount();
        // $this->addInfoMessage('Updated');
        $this->setReturnValue($counts);
    }
}