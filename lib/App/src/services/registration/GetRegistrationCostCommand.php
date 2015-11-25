<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 6:30 AM
 */

namespace App\services\registration;

use App\db\api\AttenderCostInfoDto;
use App\db\ScymRegistrationsManager;
use App\db\ScymAccountManager;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\IUser;
use Tops\sys\TUser;

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
 *        ymDonation : any; // currency or null
 *        simpleMealDonation : any;// currency or null
 *        aidRequested  : any; // currency or null???
 *        attenders : IAttender[];
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
        $registrationsManager = new ScymRegistrationsManager();
        $accountManager = new ScymAccountManager($registrationsManager);
        $attenders = $this->buildAttenderList($request);
        $costs = $accountManager->calculate($attenders);
        $accountService = new AccountService($registrationsManager);
        $summary = $accountService->formatAccountSummary($costs);
        $summary->funds = ($request->getFundList) ? $registrationsManager->getFundList() : [];
        $this->setReturnValue($summary);
    }

    private function buildAttenderList($request)
    {
        $result = array();
        foreach($request->attenders as $attender) {
            array_push($result,new AttenderCostInfoDto($attender));
        }
        return $result;
    }
}