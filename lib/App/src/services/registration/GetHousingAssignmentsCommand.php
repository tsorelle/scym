<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/20/2016
 * Time: 8:15 AM
 */

namespace App\services\registration;


use App\db\scym\ScymAttender;
use App\db\scym\ScymHousingAssignment;
use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;

/**
 * Class GetHousingAssignmentsCommand
 * @package App\services\registration
 *
 * Contract:
 * Contract:
 *      Request:
 *          {
 *              registrationId: int
 *              includeLookups: boolean
 *          }
 *
 *      Response:
 *			export interface IGetHousingAssignmentsResponse {
 *				registrationId: number;
 *				registrationName: string;
 *				assignments: IAttenderHousingAssignment[];
 *					export interface IAttenderHousingAssignment {
 *						attender: IHousingPreference,
 *							export interface IHousingPreference {
 *								attenderId : number;
 *								attenderName: string;
 *								housingPreference: number;
 *							}
 *						assignments: IHousingAssignment[];
 *							export interface IHousingAssignment {
 *								day: number;
 *								housingUnitId: number;
 *								note: string;
 *							}
 *					}
 *
 *				units: IHousingUnit[];
 *					export interface IHousingUnit {
 *						housingUnitId: any;
 *						unitname: string;
 *						description: string;
 *						capacity: number;
 *						occupants: number;
 *						housingTypeId: number;
 *						housingTypeName: string;
 *					}
 *
 *				housingTypes : ILookupItem[];
 *					export interface ILookupItem {
 *						Key: any;
 *						Text: string;
 *						Description: string;
 *					}
 *
 *			}
 *
 */

class GetHousingAssignmentsCommand extends TServiceCommand
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

        $responseData = new \stdClass();
        $responseData->registrationId = $registrationId;

        $manager = new ScymRegistrationsManager();
        $registration = $manager->getRegistration($registrationId);
        if ($registration == null) {
            $this->addErrorMessage('ERROR: No registration found for id #'.$registrationId);
            return;
        }

        $responseData->confirmed = $registration->getConfirmed();
        $responseData->registrationName = $registration->getName();
        $responseData->assignments = $manager->getRegistrationHousingAssignments($registration);
        if ($includeLookups) {
            $responseData->availability = $manager->getHousingAvailability();
            $responseData->housingTypes = $manager->getHousingTypesLookup();
            $responseData->units = $manager->getHousingUnitsList();
        }

        // $this->addInfoMessage($responseData->confirmed ? 'Confirmed' : 'NOT confirmed');

        $this->setReturnValue($responseData);
    }

}