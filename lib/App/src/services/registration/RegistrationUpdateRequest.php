<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/5/2015
 * Time: 6:33 AM
 */

namespace App\services\registration;
use App\db\api\AttenderDto;
use App\db\api\RegistrationDto;

/**
 * Class RegistrationUpdateRequest
 * @package App\services\registration
 *
 * Decodes request object for SaveRegistrationChangesCommand
 */

class RegistrationUpdateRequest
{
    /**
     * @var \stdClass
     */
    private $request;

    /**
     * @var RegistrationDto
     */
    private $registration;

    /**
     * @var AttenderDto[]
     */
    private $updatedAttenders;


    /**
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
     */


    /**
     * RegistrationUpdateRequest constructor.
     * @param $request \stdClass
     * @throws \Exception
     */
    public function __construct($request)
    {
        if (empty($request)) {
            throw new \Exception('No update request recieved');
        }
        $this->request = $request;
        if (isset($request->updatedAttenders) && is_array($request->updatedAttenders)) {
            $this->updatedAttenders = AttenderDto::CreateList($request->updatedAttenders);
        }
        else {
            throw new \Exception('No attender list in update request');
        }
        $statusId = isset($request->statusId) ? $request->statusId : 1;
        if ($statusId < 2 && count($this->updatedAttenders) > 0) {
            $request->statusId = 2;
        }
        $this->regInfo = new RegistrationDto($request->registration);

    }

    public function getRegistrationId() {
        return $this->regInfo->getRegistrationId();
    }
    public function isNew() {
        return $this->regInfo->getRegistrationId() < 1;
    }
    public function isComplete() {
        return $this->regInfo->getRegistrationCode() != null;
    }
    public function getRegistrationInfo() {
        return $this->regInfo;
    }
    public function getUpdatedAttenders() {
        return $this->updatedAttenders;
    }
    public function getDeletedAttenders() {
        return (isset($this->request->deletedAttenders) && is_array($this->request->deletedAttenders)) ?
            $this->request->deletedAttenders :
            array();
    }
    public function getDonations() {
        return   (isset($this->request->donations) && is_array($this->request->donations)) ?
            $this->request->donations :
            null;
    }
    public function getStatusId() {
        return $this->regInfo->getStatusId();
    }

    public function getSendConfirmation() {
        return isset($this->request->sendConfirmation) ? $this->request->sendConfirmation : true;
    }

    public function getPreviousBalance() {
        return isset($this->request->previousBalance) ? $this->request->previousBalance : null;
    }
}