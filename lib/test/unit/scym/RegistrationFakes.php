<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/7/2015
 * Time: 6:32 AM
 */

namespace Tops\test\unit\scym;


use App\db\scym\ScymRegistration;
use App\db\ScymRegistrationsManager;

class RegistrationFakes
{
    public static function makeFakeIAttender($id = 1, $firstName = 'Terry')
    {
        $attender = new \stdClass(); // fake
        $attender->attenderId = $id;
        $attender->firstName = $firstName;
        $attender->lastName = 'SoRelle';
        $attender->middleName = '';
        $attender->dateOfBirth = '';
        $attender->affiliationCode = 'FMA';
        $attender->otherAffiliation = '';
        $attender->firstTimer = 0;
        $attender->notes = '';
        $attender->linens = 0;
        $attender->arrivalTime = 41;
        $attender->departureTime = 72;
        $attender->vegetarian = 1;
        $attender->attended = 0;
        $attender->singleOccupant = 0;
        $attender->glutenFree = 0;
        $attender->housingTypeId = 2;
        $attender->specialNeedsTypeId = null;
        $attender->generationId = 1; // lookup: generations
        $attender->gradeLevel = null; // 'PS';'K'; 1 .. 13
        $attender->ageGroupId = null; // lookup agegroups
        $attender->creditTypeId = 0; // number; // formerly: feeCredit; lookup: creditTypes
        $attender->meals = array(
            43,
            51,
            52,
            53,
            61,
            62,
            63,
            71,
            72
        );
        return $attender;
    }

    public static function makeFakeIRegistration($code)
    {
        $dto = new \stdClass();
        $dto->active = 1;
        $dto->year = '2016';
        $dto->registrationCode = $code;
        $dto->registrationId = -1;
        $dto->statusId = 1;
        $dto->name = 'Unit Test One';
        $dto->address = 'address';
        $dto->city = 'city';
        $dto->phone = 'phone';
        $dto->email = 'e@mail.com';
        $dto->notes = 'notes';
        $dto->financialAidAmount = 0.00;
        return $dto;
    }

}