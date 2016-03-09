<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/28/2016
 * Time: 8:23 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class UpdateHousingUnitsCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer housing");
    }
    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No request recieved.');
            return;
        }
        $updated = isset($request->updated) ? $request->updated : array();
        $active = isset($request->active) ? $request->active : array();

        $expected = (count($updated) + count($active));
        $manager = new ScymRegistrationsManager();
        $updateCount = $manager->updateHousingUnits($updated,$active);
        if ($expected != $updateCount) {
            $this->addWarningMessage("Some updates failed.");
        }

        $response = new \stdClass();
        $response->units = $manager->getHousingUnits();
        $this->setReturnValue($response);

    }
}