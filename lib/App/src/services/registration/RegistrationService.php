<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/18/2015
 * Time: 11:52 AM
 */

namespace App\services\registration;


use App\db\scym\ScymRegistration;
use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use MyProject\Proxies\__CG__\stdClass;

class RegistrationService
{
    /**
     * @var ScymRegistrationsManager
     */
    private $manager;

    public function __construct(ScymRegistrationsManager $manager)
    {
        $this->manager = $manager;
    }

    /*
     *  Request
     *      {
     *          type: 'id' | 'code'
     *          value: int | string
     *          year: 'nnnn' | optional / null
     *      }
     *
     *  Response
     *    export interface IRegistrationResponse {
     *    	  registration: IRegistrationInfo;
     *    	  accountSummary: IAccountSummary;
     *    	  attenderList: IListItem[];
     *    	  housingAssignments: IListItem[];
     *    }
     */

    /**
     * @param \stdClass $request
     * @return bool|\StdClass   (response)
     * @throws \Exception
     */
    public function getRegistration(\stdClass $request) {
        /**
         * @var $registration ScymRegistration
         */
        $type =  isset($request->type) ? $request->type : 'not assigned';
        switch($type) {
            case 'id' :
                $registration =  $this->manager->getRegistration($request->value);
                break;
            case 'code' :
                $year = isset($request->year) ? $request->year : null;
                if ($year == null) {
                    $session = $this->manager->getSession();
                    $year = $session->getYear();
                }
                $registration =  $this->manager->getRegistrationByCode($request->value,$year);
                break;
            default:
                $registration = null;
                break;
        }

        if ($registration == null) {
            return false;
        }

        // build account and summary
        $accountManager = new ScymAccountManager($this->manager);
        $account = $accountManager->getAccountFromRegistration($registration);

        // build response
        $accountService = new AccountService($this->manager);
        $response = new \StdClass();
        $response->accountSummary = $accountService->formatAccountSummary($account);
        $getFundList = isset($request->getFundList) ? $request->getFundList : false;
        $response->accountSummary->funds = $getFundList ? $this->manager->getFundList() : [];
        $response->registration = $registration->getDataTransferObject();
        $response->attenderList = $registration->getAttenderList();
        // todo: retrieve and format housing assignments
        $response->housingAssignments = array();

        return $response;
    }
}