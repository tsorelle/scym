<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 10:01 AM
 */
namespace App\db\api;

interface IAttenderCostInfo
{
    /**
     * Get attenderid
     *
     * @return integer
     */
    public function getAttenderId();

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname();

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName();

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename();

    /**
     * Get financialaidrequested
     *
     * @return boolean
     *
    public function getFinancialAidRequested();
*/

    /**
     * Get linens
     *
     * @return boolean
     */
    public function getLinens();

    /**
     * Get arrivaltime
     *
     * @return boolean
     */
    public function getArrivalTime();

    /**
     * Get departuretime
     *
     * @return boolean
     */
    public function getDeparturetime();

    /**
     * Get housingTypeId
     *
     * @return integer
     */
    public function getHousingTypeId();

    /**
     * Get generationId
     *
     * @return boolean
     */
    public function getGenerationId();

    /**
     * Get feeCreditId
     *
     * @return integer
     */
    public function getCreditTypeId();

    /**
     * Get singleoccupant
     *
     * @return boolean
     */
    public function getSingleOccupant();

    /**
     * @return int[]
     */
    public function getMeals();

    /**
     * Get specialneeds
     *
     * @return integer
     */
    public function getSpecialNeedsTypeId();

}