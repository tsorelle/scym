<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 11:42 AM
 */

namespace App\db\api;


class AttenderCostInfoDto implements IAttenderCostInfo
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
        return $this->data->middleName;
    }

    /**
     * Get financialaidrequested
     *
     * @return boolean
    public function getFinancialAidRequested()
    {

    }
     */

    /**
     * Get linens
     *
     * @return boolean
     */
    public function getLinens()
    {
        return $this->data->linens;
    }

    /**
     * Get arrivaltime
     *
     * @return boolean
     */
    public function getArrivalTime()
    {
        return $this->data->arrivalTime;
    }

    /**
     * Get departuretime
     *
     * @return boolean
     */
    public function getDeparturetime()
    {
        return $this->data->departureTime;
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
     * @return boolean
     */
    public function getGenerationId()
    {
        return $this->data->generationId; // lookup: generations
    }

    /**
     * Get feeCreditId
     *
     * @return integer
     */
    public function getCreditTypeId()
    {
        return $this->data->creditTypeId; // lookup: creditTypes
    }

    /**
     * Get singleoccupant
     *
     * @return boolean
     */
    public function getSingleOccupant()
    {
        return $this->data->singleOccupant;
    }

    /**
     * @return int[]
     */
    public function getMeals()
    {
        return $this->data->meals;
    }
}