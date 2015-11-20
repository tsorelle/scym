<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 8:35 AM
 */

namespace App\services\registration;


interface IRegistrationCostRequest
{
    public function getYmDonation();
    public function getSimpleMealDonantion();
    public function getAidRequested();

    /**
     * @return IAttenderCostRequest[]
     */
    public function getAttenders();
}