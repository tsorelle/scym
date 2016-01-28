<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/26/2016
 * Time: 5:58 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetHousingUnitsCommand extends TServiceCommand
{

    protected function run()
    {
        $typesRequest = 'all';
        $unitsRequest = 'all';

        $request = $this->getRequest();
        if ($request != null) {
            $typesRequest = isset($request->types) ? $request->types : 'all';
            $unitsRequest = isset($request->units) ? $request->units : 'none';
        }

        $manager = new ScymRegistrationsManager();
        $response = new \stdClass();
        $activeUnitsOnly = ($unitsRequest == 'active');
        $response->units = $manager->getHousingUnits($activeUnitsOnly);
        if ($typesRequest == 'none') {
            $response->types = null;
        }
        else {
            $activeOnly = ($typesRequest == 'active');
            $response->types = $manager->getHousingTypesLookup($activeOnly);
        }
        $this->setReturnValue($response);
    }
}