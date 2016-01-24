<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/24/2016
 * Time: 6:39 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetHousingTypesEditListCommand extends TServiceCommand
{

    protected function run()
    {
        $manager = new ScymRegistrationsManager();
        $result = $manager->getHousingTypesEditList();
        $this->setReturnValue($result);
    }
}