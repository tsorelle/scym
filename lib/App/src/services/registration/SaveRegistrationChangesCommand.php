<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/3/2015
 * Time: 7:40 AM
 */
namespace App\services\registration;

use App\db\api\AttenderDto;
use App\db\api\RegistrationAccount;
use App\db\api\RegistrationDto;
use App\db\scym\ScymAttender;
use App\db\scym\ScymRegistration;
use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class SaveRegistrationChangesCommand extends TServiceCommand
{
    /**
     *
     *  ******** Request ********************************
     *      export interface IRegistrationInfo {
     *          registrationId : any;
     *          active : number;
     *          year : string;
     *          registrationCode : string;
     *          statusId : number; // lookup: registration statustypes
     *          name : string;
     *          address : string;
     *          city : string;
     *          phone : string;
     *          email : string;
     *          receivedDate : any;
     *          amountPaid : any;
     *          notes : string;
     *          feesReceivedDate : any;
     *          arrivalTime : string;
     *          departureTime : string;
     *          scymNotes : string;
     *          statusDate : any;
     *          confirmed: number;
     *          aidRequested  : any;
     *      }
     *
     *      export interface IAttender {
     *          attenderId: any;
     *          firstName : string;
     *          lastName : string;
     *          middleName : string;
     *          dateOfBirth : any;
     *          affiliationCode : string;
     *          otherAffiliation : string;
     *          firstTimer : number;
     *          teacher : number;
     *          financialAidRequested : number;
     *          guest : number;
     *          notes : string;
     *          linens : number;
     *          arrivalTime  : string;
     *          departureTime  : string;
     *          vegetarian : number;
     *          attended : number;
     *          singleOccupant : number;
     *          glutenFree : number;
     *          changed: boolean;
     *          housingTypeId : any;
     *          specialNeedsTypeId : any; // lookup: special needs
     *          generationId : any; // lookup: generations
     *          gradeLevel : string; // 'PS','K', 1 .. 13
     *          ageGroupId : any; // lookup agegroups
     *          creditTypeId : number; // formerly: feeCredit, lookup: creditTypes
     *          meals: number[];
     *      }
     *
     *      export interface IRegistrationUpdateRequest {
     *          registration : IRegistrationInfo;
     *          updatedAttenders : IAttender[];
     *          deletedAttenders : number[];
     *          contributions: IKeyValuePair[];
     *      }
     *
     *
     *  ******** Response ********************************
     *
     *      export interface IPaymentItem {
     *          paymentId : any;
     *          dateReceived : any; // date
     *          amount: number; // currency
     *          checkNumber : string; // number or 'cash'
     *          payor : string;
     *      }
     *      export interface ILookupItem {
     *          Key: any;
     *          Text: string;
     *          Description: string;
     *      }
     *
     *      export interface IListItem {
     *          Text: string;
     *          Value: any;
     *          Description: string;
     *      }
     *
     *      export interface IIndexedItem extends IListItem {
     *          Key: any;
     *      }
     *
     *   	export interface IAccountSummary {
     *          funds: ILookupItem[];
     *          fees : IListItem[];
     *          credits: IListItem[];
     *          donations: IIndexedItem[];
     *          payments: IPaymentItem[];
     *          feeTotal: string;
     *          creditTotal: string;
     *          donationTotal: string;
     *          aidEligibility: string;
     *          balance: any; // number
     *      }
     *
     *      export interface IRegistrationResponse {
     *          registration: IRegistrationInfo;  // see request
     *          accountSummary: IAccountSummary;
     *          attenderList: IListItem[];
     *          housingAssignments: IListItem[];
     *      }
     *
     */
    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null || !isset($request->registration)) {
            throw new \Exception('No request received.');
        }

        // extract request elements
        $regInfo = new RegistrationDto($request->registration);
        $regId = $regInfo->getRegistrationId();
        $isNew = ($regId < 1);
        $registrationsManager = new ScymRegistrationsManager();

        $updatedAttenders = (isset($request->updatedAttenders) && is_array($request->updatedAttenders)) ?
            AttenderDto::CreateList($request->updatedAttenders) :
            array();
        $donations = (isset($request->contributions) && is_array($request->contributions)) ?
            $request->contributions :
            array();

        $statusId = $regInfo->getStatusId();
        if ($statusId < 2 && count($updatedAttenders) > 0) {
            $regInfo->setStatusId(2);
        }

        if ($isNew) {
            // create new registration
            $registration = ScymRegistration::createNewRegistration($regInfo);
            $registration->addAttenders($updatedAttenders);
        }
        else {
            // update existing
            $registration = $registrationsManager->getRegistration($regId);
            $registration->updateFromDataTransferObject($regInfo);
            if (isset($request->deletedAttenders) && is_array($request->deletedAttenders)) {
                $registrationsManager->deleteAttenders($registration,$request->deletedAttenders);
            }
            $registration->updateAttenders($updatedAttenders);
            $registrationsManager->clearAccountItems($registration);
        }



        // save initial changes
        $registrationsManager->updateEntity($registration);


        // build account and summary
        $accountManager = new ScymAccountManager($registrationsManager);
        $account = $accountManager->createAccount($registration->getAttenders()->toArray(),
            $donations,$registration->getFinancialAidRequested() );
        $registration->addAccountItems($account);
        $registrationsManager->updateEntity($registration); // save account items

        // build response
        $accountService = new AccountService($registrationsManager);
        $response = new \StdClass();
        $response->accountSummary = $accountService->formatAccountSummary($account);
        $response->registration = $registration->getDataTransferObject();
        $response->attenderList = $registration->getAttenderList();
        // todo: retrieve and format housing assignments
        $response->housingAssignments = array();

        $this->setReturnValue($response);

    }
}