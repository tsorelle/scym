<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/21/2016
 * Time: 4:33 PM
 */

namespace App\services\registration;


use App\db\scym\ScymAttender;
use App\db\scym\ScymHousingAssignment;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

/**
 * Class UpdateHousingAssignmentsCommand
 * @package App\services\registration
 *
 * Service Contract
 * Request:
 * 	export interface IHousingAssignmentUpdateRequest {
 * 	registrationId: any,
 * 	updates: IHousingAssignmentsUpdate[];
 * 		export interface IHousingAssignmentsUpdate {
 * 			attenderId: number;
 * 			assignments: IHousingAssignment[];
 * 				export interface IHousingAssignment {
 * 					day: number;
 * 					housingUnitId: number;
 * 					note: string;
 * 				}
 * 		}
 * 	}
 *
 * Response: none
 *
 */
class UpdateHousingAssignmentsCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer housing");
    }

    protected function run()
    {
        $request = $this->getRequest();
        if ($request == null) {
            $this->addErrorMessage('ERROR: No request recieved');
            return;
        }
        $registrationId = isset($request->registrationId) ? $request->registrationId : null;
        if (empty($registrationId)) {
            $this->addErrorMessage('ERROR: No registration id.');
            return;
        }

        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage('ERROR: No registration found for id ' . $registrationId);
            return;
        }

        $session = $manager->getSession();
        $today = new \DateTime();

        if ($today >= $session->getStart()) {
            $registration->setConfirmed(true);
        }

        $attenders = $registration->getAttenders()->toArray();
        $updates = isset($request->updates) ? $request->updates : null;
        if ($updates == null) {
            $this->addErrorMessage('No updates recieved.');
            return;
        }
        $updateCount = 0;
        foreach ($updates as $update) {
            /**
             * @var ScymAttender $attender
             */
            $attenderId = isset($update->attenderId) ? $update->attenderId : null;
            if ($attenderId = null) {
                $this->addErrorMessage("Error: invaild request. No attenderid");
                return;
            }
            $attender = $this->getAttender($attenders, $update->attenderId);
            if ($attender == null) {
                $this->addErrorMessage("Attender #$update->attenderId not found.");
                return;
            }
            $assignments = $attender->getHousingAssignments()->toArray();
            $assignmentUpdates = isset($update->assignments) ? $update->assignments : null;
            if (!empty($assignmentUpdates)) {
                foreach ($assignmentUpdates as $assignmentUpdate) {
                    $day = isset($assignmentUpdate->day) ? $assignmentUpdate->day : null;
                    $housingUnitId = isset($assignmentUpdate->housingUnitId) ? $assignmentUpdate->housingUnitId : null;
                    if ($day == null || $housingUnitId == null) {
                        $this->addErrorMessage("Error: invaild request (housing update).");
                        return;
                    }

                    /**
                     * @var ScymHousingAssignment $assignment
                     */
                    $assignment = $this->getAssignment($assignments, $day);
                    if ($assignment == null) {
                        $assignment = new ScymHousingAssignment();
                        $assignment->setDay($day);
                        $assignment->setHousingUnitId($housingUnitId);
                        $attender->addHousingAssignment($assignment);
                        $updateCount++;
                    } else if ($assignment->getHousingAssignmentId() != $housingUnitId) {
                        $assignment->setHousingUnitId($housingUnitId);
                        $updateCount++;
                    }
                }
            }
        }
        if ($updateCount) {
            $this->addInfoMessage("$updateCount assignments updated");
            $manager->updateEntity($registration);
        } else {
            $this->addWarningMessage('No assignments were added or changed.');
        }
    }


    private function getAttender(array $attenders,$id) {
        foreach($attenders as $attender) {
            /**
             * @var ScymAttender $attender
             */
            if ($attender->getAttenderId() == $id) {
                return $attender;
            }
        }
        return null;
    }

    /**
     * @param array $assignments
     * @param $day
     * @return ScymHousingAssignment
     */
    private function getAssignment(array $assignments, $day)
    {
        foreach($assignments as $assignment) {
            /**
             * @var ScymHousingAssignment $assignment
             */
            if ($assignment->getDay()  == $day) {
                return $assignment;
            }
        }
        return null;
    }


}