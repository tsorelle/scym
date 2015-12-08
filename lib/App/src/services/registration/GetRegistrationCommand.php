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
     */

    protected function run()
    {
        $request = $this->getRequest();
        if (empty($request)) {
            throw new Exception('Invalid lookup value in GetRegistrationCommand');
        }

        $manager = new ScymRegistrationsManager();
        /**
         * @var $registration ScymRegistration
         */
        $type =  isset($request->type) ? $request->type : 'not assigned';
        switch($type) {
            case 'id' :
                $registration =  $manager->getRegistration($request->value);
                break;
            case 'code' :
                $registration =  $manager->getRegistrationByCode($request->value);
                break;
            default:
                $registration = null;
                break;
        }

        if ($registration == null) {
            $this->addErrorMessage("Registration '$request->value' was not found.");
            return;
        }

        // build account and summary
        $accountManager = new ScymAccountManager($manager);
        $account = $accountManager->getAccountFromRegistration($registration);

        // build response
        $accountService = new AccountService($manager);
        $response = new \StdClass();
        $response->accountSummary = $accountService->formatAccountSummary($account);
        $getFundList = isset($request->getFundList) ? $request->getFundList : false;
        $response->accountSummary->funds = $getFundList ? $manager->getFundList() : [];

        $response->registration = $registration->getDataTransferObject();
        $response->attenderList = $registration->getAttenderList();
        // todo: retrieve and format housing assignments
        $response->housingAssignments = array();

        $this->setReturnValue($response);

    }
}