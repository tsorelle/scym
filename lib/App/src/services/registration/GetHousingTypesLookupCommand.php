<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/26/2016
 * Time: 6:29 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetHousingTypesLookupCommand extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        $includeActive = ($request == 'all');
        $manager = new ScymRegistrationsManager();
        $types = $manager->getHousingTypesLookup($includeActive);
        $this->setReturnValue($types);
    }
}