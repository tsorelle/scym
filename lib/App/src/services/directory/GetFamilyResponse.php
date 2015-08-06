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
    public static function BuildResponseForAddress(ScymAddress $address)
    {
        $result = new \stdClass();
        $result->persons = array();
        $result->address = $address->getDataTransferObject();
        $persons = $address->getPersons();
        if (!empty($persons)) {
            foreach($persons as $addrPerson) {
                $dto = $addrPerson->getDataTransferObject();
                array_push($result->persons, $dto);
            }
        }
        return $result;
    }


    public static function BuildResponseForPerson(ScymPerson $person)
    {
        if ($person != null) {
            $address = $person->getAddress();
            if ($address != null) {
                return self::BuildResponseForAddress($address);
            }
        }

        $result = new \stdClass();
        $result->persons = array();
        $result->address = null;
        $dto = $person->getDataTransferObject();
        array_push($result->persons, $dto);
        return $result;
    }

}