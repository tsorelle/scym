<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/3/2015
 * Time: 7:40 AM
 */
namespace App\services\registration;

use App\db\scym\ScymRegistration;
use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;
use Tops\sys\TContentManager;
use Tops\sys\TDateTime;
use Tops\sys\TPostOffice;
use Tops\sys\TTextTemplate;
use Tops\sys\TUser;

class SaveRegistrationChangesCommand extends TServiceCommand
{
    /**
     *  Service API
     *
     *  ******** Request ********************************
     *  @see App\services\registration\RegistrationUpdateRequest
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

    /**
     * @var ScymRegistrationsManager
     */
    private $registrationsManager;
    
    protected function run()
    {
        $request =  new RegistrationUpdateRequest( $this->getRequest());
        $this->registrationsManager = new ScymRegistrationsManager();

        $registration = $request->isNew() ?
            $this->newRegistration($request) :
            $this->updateRegistration($request);

        $this->registrationsManager->updateDonations($request->getDonations(),$registration);

        $attenders = $registration->getAttenders()->toArray();
        $recieved = ($registration->getStatusId() == 1 && count($attenders));
        if ($recieved) {
            $registration->setStatusId(2);
        }

        try {
            // save initial changes
            $this->registrationsManager->updateEntity($registration);
        }
        catch (\Exception $ex) {
            throw($ex);
        }


        // build account and summary
        $accountManager = new ScymAccountManager($this->registrationsManager);
        $account = $accountManager->createAccountFromRegistration($registration);
        $this->registrationsManager->updateEntity($registration); // save account items

        // build response
        $accountService = new AccountService($this->registrationsManager);
        $response = new \StdClass();
        $response->accountSummary = $accountService->formatAccountSummary($account);
        $response->registration = $registration->getDataTransferObject();
        $response->attenderList = $registration->getAttenderList();
        // todo: retrieve and format housing assignments
        $response->housingAssignments = array();

        if ($recieved) {
            $this->sendConfirmationMessage($response);
        }

        $this->setReturnValue($response);

    }

    /**
     * @param $request RegistrationUpdateRequest
     * @return ScymRegistration
     */
    private function newRegistration($request)
    {
        $regInfo = $request->getRegistrationInfo();
        $registration = ScymRegistration::createNewRegistration($regInfo);
        $registration->addAttenders($request->getUpdatedAttenders());
        $user = TUser::getCurrent();
        if ($user->isAuthenticated()) {
            $userName = $user->getUserName();
            $userRegId = $this->registrationsManager->getUserRegistrationId($userName,$regInfo->getYear());
            if (!$userRegId) {
                $registration->setUsername($userName);
            }
        }
        return $registration;
    }

    /**
     * @param $request RegistrationUpdateRequest
     * @return ScymRegistration
     */
    protected function updateRegistration($request)
    {
        $registration = $this->registrationsManager->getRegistration($request->getRegistrationId());
        if ($request->isComplete()) {
            $registration->updateFromDataTransferObject($request->getRegistrationInfo());
        }
        $this->registrationsManager->deleteAttenders($registration, $request->getDeletedAttenders());
        $attenders = $request->getUpdatedAttenders();
        $removeYouthList = $registration->updateAttenders($attenders);
        foreach($removeYouthList as $updatedAttender) {
            $this->registrationsManager->deleteYouth($updatedAttender);
        }
        $this->registrationsManager->clearAccountItems($registration);
        return $registration;
    }

    /**
     * @param $response
     */
    protected function sendConfirmationMessage($response)
    {
        $session = $this->registrationsManager->getSession();
        $tokens = array(
            'name' => $response->registration->name,
            'dates' => TDateTime::formatRange($session->getStart(),$session->getEnd()),
            'code' => $response->registration->registrationCode,
            'cost' => $response->accountSummary->balance
        );
        $template = TContentManager::GetText('templates/registration-response');
        $messageText = TTextTemplate::Merge($tokens, $template);
        $message = TPostOffice::CreateMessageFromUs('registrar', 'SCYM Registration', $messageText);
        if ($message == null) {
            return; // in some unit test scenarios $message is not created. Ignore for these cases.
        }
        $message->setRecipient($response->registration->email);
        $registrarAddress = TPostOffice::GetMailboxAddress('registrar');
        $message->addCC($registrarAddress->getEmail(), $registrarAddress->getName());
        $sendResult = TPostOffice::Send($message);
        if ($sendResult === false) {
            $this->addInfoMessage('The mail service failed, so your confirmation was not sent. You registration has been recieved anyway.');
        }
    }

}