<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 8:29 AM
 */

namespace App\services\registration;


interface IAttenderCostRequest
{
    public function getAttenderId();
    public function getFirstName();
    public function getLastName();
    public function getMiddleName();
    public function getLinens();
    public function getArrivalTime();
    public function getDepartureTime();
    public function getSingleOccupant();
    public function getHousingTypeId();
    public function getGenerationId();
    public function getCreditTypeId();
    public function getMeals();
}