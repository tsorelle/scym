<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/16/2016
 * Time: 11:00 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetAgeGroupListCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        $all = ($request == 'all');
        $manager = new ScymRegistrationsManager();
        $result = $manager->getAgeGroupList($all);
        $this->setReturnValue($result);
    }
}