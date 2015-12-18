<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/7/2015
 * Time: 6:01 AM
 */

namespace App\services\registration;


use App\db\scym\ScymRegistration;
use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Tops\services\TServiceCommand;

class GetRegistrationCommand extends TServiceCommand
{

    /**
     *  Request
     *      {
     *          type: 'id' | 'code'
     *          value: int | string
     *      }
     *
     *  Response
     *    export interface IRegistrationResponse {
     *    	  registration: IRegistrationInfo;
     *    	  accountSummary: IAccountSummary;
     *    	  attenderList: IListItem[];
     *    	  housingAssignments: IListItem[];
     *    }
     *
     */


    protected function run()
    {
        $request = $this->getRequest();
        if (empty($request)) {
            throw new Exception('Invalid lookup value in GetRegistrationCommand');
        }

        $manager = new ScymRegistrationsManager();
        $registrationService = new RegistrationService($manager);
        $response = $registrationService->getRegistration($request);
        if (empty($response)) {
            $this->addErrorMessage("Registration '$request->value' was not found.");
            return;
        }

        $this->setReturnValue($response);

    }
}