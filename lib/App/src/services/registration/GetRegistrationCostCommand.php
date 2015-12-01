<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 6:30 AM
 */

namespace App\services\registration;

use App\db\api\AttenderCostFacade;
use App\db\api\IAttenderCostInfo;
use App\db\ScymRegistrationsManager;
use App\db\ScymAccountManager;
use Tops\services;
use Tops\services\TServiceCommand;

/**
 * Class GetRegistrationCostCommand
 * @package App\services\registration
 *
 * Calculate registration cost without saving changes.
 *
 * Service Contract
 *	Request
 *   ===========
 *    export interface ICostUpdateRequest {
 *        attenders : IAttender[];
 *        deletedAttenders : any[]; // id numbers of attenders to be deleted
 *        donations: IKeyValuePair[];  // Key = donationTypeId, Value = amount (currency)
 *        aidRequested  : any; // currency or null
 *    }
 *
 *	export interface IAttender {  // relevant data only other fields ignored
 *        attenderId: any;
 *        firstName : string;
 *        lastName : string;
 *        middleName : string;
 *        linens : number;// 0 or 1
 *        arrivalTime  : string;
 *        departureTime  : string;
 *        singleOccupant : number;
 *        housingTypeId : any;
 *        generationId : any; // lookup: generations
 *        creditTypeId : number; // formerly: feeCredit, lookup: creditTypes
 *        meals: number[];
 *    }
 *
 *	Response
 *   ===========
 *    export interface IAccountSummary {
 *        fees : IListItem[];
 *        credits: IListItem[];
 *        donations: IListItem[];
 *        payments: IPaymentItem[];
 *        feeTotal: string;
 *        creditTotal: string;
 *        donationTotal: string;
 *        balance: any;
 *    }
 *    export interface IPaymentItem {
 *        paymentId : any;
 *        dateReceived : any; // date
 *        amount: number; // currency
 *        checkNumber : string; // number or 'cash'
 *        payor : string;
 *    }
 *    export interface IListItem {
 *        Text: string;
 *        Value: any;
 *        Description: string;
 *    }
 */
class GetRegistrationCostCommand  extends TServiceCommand
{

    protected function run()
    {
        $request = $this->getRequest();
        if (!$request || !$request->attenders) {
            $this->addErrorMessage('Invalid request received.');
            return;
        }
        $attenders = $this->buildAttenderList($request);
        $registrationsManager = new ScymRegistrationsManager();
        $accountManager = new ScymAccountManager($registrationsManager);
        $costs = $accountManager->calculate($attenders);
        $accountService = new AccountService($registrationsManager);
        $summary = $accountService->formatAccountSummary($costs);
        $summary->funds = ($request->getFundList) ? $registrationsManager->getFundList() : [];
        $this->setReturnValue($summary);
    }

    /**
     * @param $request
     * @return IAttenderCostInfo[]
     */
    private function buildAttenderList($request)
    {
        $result = array();
        foreach($request->attenders as $attender) {
            array_push($result,new AttenderCostFacade($attender));
        }
        return $result;
    }

}