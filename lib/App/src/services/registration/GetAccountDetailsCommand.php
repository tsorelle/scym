<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/10/2016
 * Time: 11:46 AM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetAccountDetailsCommand extends TServiceCommand
{
    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No service request.');
            return;
        }
        $registrationId = isset($request->registrationId) ? $request->registrationId : 0;
        if (empty($registrationId)) {
            $this->addErrorMessage('ERROR: No registration id.');
            return;
        }
        $includeLookups = isset($request->includeLookups) ? (!empty($request->includeLookups)) : false;

        $manager = new ScymRegistrationsManager();
        $responseData = $manager->getAccountDetails($registrationId,$includeLookups);
        $this->setReturnValue($responseData);
    }
}