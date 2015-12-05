<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 11:42 AM
 */

namespace App\db\api;


use Tops\sys\TDateTime;

class AttenderDto implements IAttenderCostInfo
{
    private $data;

    function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Get attenderid
     *
     * @return integer
     */
    public function getAttenderId()
    {
        return $this->data->attenderId;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->data->firstName;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->data->lastName;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return isset($this->data->middleName) ? $this->data->middleName : null;
    }


    /**
     * Get linens
     *
     * @return boolean
     */
    public function getLinens()
    {
        return isset($this->data->linens) ? $this->data->linens : false;
    }

    /**
     * Get arrivaltime
     *
     * @return int
     */
    public function getArrivalTime()
    {
        return isset($this->data->arrivalTime) ? $this->data->arrivalTime : 41;
    }

    /**
     * Get departuretime
     *
     * @return int
     */
    public function getDeparturetime()
    {
        return isset($this->data->departureTime) ? $this->data->departureTime : 72;
    }

    /**
     * Get housingTypeId
     *
     * @return integer
     */
    public function getHousingTypeId()
    {
        return $this->data->housingTypeId;
    }

    /**
     * Get generationId
     *
     * @return int
     */
    public function getGenerationId()
    {
        return isset($this->data->generationId) ? $this->data->generationId : 1;
    }

    /**
     * Get feeCreditId
     *
     * @return integer
     */
    public function getCreditTypeId()
    {
        return isset($this->data->creditTypeId) ? $this->data->creditTypeId : 1;
    }

    /**
     * Get singleoccupant
     *
     * @return boolean
     */
    public function getSingleOccupant()
    {
        return isset($this->data->singleOccupant) ? !empty($this->data->singleOccupant) : false;
    }

    /**
     * @return int[]
     */
    public function getMeals()
    {
        return isset($this->data->meals) ? $this->data->meals : null;
    }

    /**
     * Get specialneeds
     *
     * @return integer
     */
    public function getSpecialNeedsTypeId()
    {
        return isset($this->data->specialNeedsTypeId) ? $this->data->specialNeedsTypeId : 0;
    }

    /**
     * Get dateofbirth
     *
     * @return \DateTime
     */
    public function getDateofbirth() {
        try {
            $result = isset($this->data->dateOfBirth) ? new \DateTime($this->data->dateOfBirth) : null;
            if (TDateTime::isEmpty($result)) {
                return null;
            }
        }
        catch (\Exception $ex) {
            // return null if invalid date
            return null;
        }
    }

    /**
     * Get affiliationcode
     *
     * @return string
     */
    public function getAffiliationCode() {
        return isset($this->data->affiliationCode) ? $this->data->affiliationCode : 'NONE';
    }

    /**
     * Get otheraffiliation
     *
     * @return string
     */
    public function getOtherAffiliation() {
        return isset($this->data->affiliationCode) ? $this->data->affiliationCode : 'NONE';
    }

    /**
     * Get firsttimer
     *
     * @return boolean
     */
    public function getFirstTimer() {
        return isset($this->data->firstTimer) ?   !empty($this->data->firstTimer)  : false;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes() {
        return isset($this->data->notes ) ? $this->data->notes   : null;
    }

    /**
     * Get vegetarian
     *
     * @return boolean
     */
    public function getVegetarian() {
        return isset($this->data->vegetarian) ?   !empty($this->data->vegetarian)  : false;
    }

    /**
     * Get gradelevel
     *
     * @return string
     */
    public function getGradeLevel() {
        return isset($this->data->gradeLevel) ? $this->data->gradeLevel : null;
    }

    /**
     * Get agegroup
     *
     * @return integer
     */
    public function getAgeGroupId() {
        return isset($this->data->ageGroupId) ? $this->data->ageGroupId : null;
    }

    /**
     * Get glutenfree
     *
     * @return boolean
     */
    public function getGlutenfree() {
        return isset($this->data->ageGroupId) ? $this->data->ageGroupId : null;
    }

    public function getAttended() {
        return isset($this->data->attended) ?   !empty($this->data->attended)  : false;
    }

    public static function CreateList(array $attenders)
    {
        $result = array();
        foreach($attenders as $attender) {
            array_push($result,new AttenderDto($attender));
        }
        return $result;
    }



}