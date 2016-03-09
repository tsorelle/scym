<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 11:34 AM
 */

namespace App\services\directory;


use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;

class GetFamilyResponse
{
    public static function BuildResponseForAddress(ScymAddress $address,$selectedPersonId = 0)
    {
        $result = new \stdClass();
        $result->persons = array();
        $result->address = $address->getDataTransferObject();
        $persons = $address->getPersons();
        if (!empty($persons)) {
            foreach($persons as $addrPerson) {
                if ($addrPerson->getActive()) {
                    $dto = $addrPerson->getDataTransferObject();
                    array_push($result->persons, $dto);
                }
            }
        }
        $result->selectedPersonId = $selectedPersonId;
        return $result;
    }


    public static function BuildResponseForPerson(ScymPerson $person)
    {

        if ($person != null) {
            $address = $person->getAddress();
            if ($address != null) {
                return self::BuildResponseForAddress($address,$person->getPersonid());
            }
        }

        $result = new \stdClass();
        $result->persons = array();
        $result->address = null;
        $result->selectedPersonId = $person->getPersonid();
        $dto = $person->getDataTransferObject();
        array_push($result->persons, $dto);
        return $result;
    }

}