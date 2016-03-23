<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/7/2016
 * Time: 5:36 AM
 */

namespace App\services\registration;


use App\db\ScymAccountManager;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

class GetRegistrationDashboardCommand extends TServiceCommand
{
    /****
     * Service contract
     *
     *	Request
     *	-------------------------
     *	registrationId : number
     *
     *	Response
     *	---------------------------------
     *    export interface IRegistrationDashboardResponse {
     *        registrationId: any,
     *        registrationCode: string,
     *        name: string,
     *        address: string,
     *        city: string,
     *        phone: string,
     *        email: string,
     *        notes: string,
     *        status: number,
     *        statusText: string,
     *        balanceDue: any,
     *        attenders: IAttenderCheckListItem[];
     *        housingAssignment: string;
     *    }
     *    export interface IAttenderCheckListItem {
     *        attenderId: any;
     *        arrived: any;
     *        name: string;
     *        ageGroup: string;
     *        dietPreference: string;
     *        specialNeeds: string;
     *        firstTimer: string,
     *        meeting: string,
     *        note: string;
     *        linens: string,
     *        housingAssignments : IHousingInfoItem[];
     *    }
     */

    public function __construct() {
        $this->addAuthorization("administer registrations");
    }

    protected function run()
    {
        $registrationId = $this->getRequest();
        if (empty($registrationId)) {
            $this->addErrorMessage('No request id recieved.');
            return;
        }

        $responseDto = new \stdClass();

        $manager = new ScymRegistrationsManager();
        $registration =  $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage("Registration #$registrationId not found.");
            return;
        }

        $responseDto->registrationId =  $registrationId;
        $responseDto->registrationCode =  $registration->getRegistrationCode();
        $responseDto->name =  $registration->getName();
        $responseDto->address =  $registration->getAddress();
        $responseDto->city =  $registration->getCity();
        $responseDto->phone =  $registration->getPhone();
        $responseDto->email =  $registration->getEmail();
        $responseDto->notes =  $registration->getNotes();
        $responseDto->status =  $registration->getStatusId();
        $responseDto->confirmed = $registration->getConfirmed();
        $responseDto->registrarNotes = $registration->getScymNotes();
        switch($responseDto->status) {
            case 0 : $responseDto->statusText = 'Cancelled' ; break;
            case 1 : $responseDto->statusText = 'Incomplete'; break;
            case 2 : $responseDto->statusText = 'Submitted' ; break;
            case 3 : $responseDto->statusText = 'Completed' ; break;
            case 4 : $responseDto->statusText = 'Attended'  ; break;
            default : $responseDto->statusText = 'Unknown status'  ; break;
        }
        $responseDto->attenders = $manager->getAttendersViewForRegistration($registrationId);
        $housingList = $manager->getHousingAssignmentView($registrationId);
        $responseDto->housingAssignment = $this->getHousingAssignmentText($housingList);
        foreach($responseDto->attenders as $attender) {
            $attender->housingAssignments = $this->getHousingAssignments($attender->attenderId,$housingList);
        }
        $accountManager = new ScymAccountManager($this->manager);
        $account = $accountManager->getAccountFromRegistration($registration);
        $responseDto->balanceDue = $account->getBalance();

        $this->setReturnValue($responseDto);

    }

    private function getHousingAssignmentText($assignments)
    {
        $result = null;
        foreach ($assignments as $assignment) {
            if ($result == null) {
                $result = $assignment->unit;
            }
            else {
                if ($assignment->unit != $result) {
                    return 'Varies, see attender.';
                }
            }
            // if ($assignment->unit)
        }
        return $result == null ? 'No assignments' : $result;
    }

    private function getHousingAssignments($attenderId,$housingList) {
        $result = array();
        foreach($housingList as $assignment) {
            if ($assignment->attenderId == $attenderId) {
                $item = new \stdClass();
                $item->day = $assignment->day;
                $item->unit = $assignment->unit;
                array_push($result,$item);
            }
        }
        return $result;
    }

}