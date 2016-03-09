<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 10:56 AM
 */

namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;



class GetFamilyCommand extends TServiceCommand
{

    public function __construct() {
        $this->addAuthorization("view directory");
    }

    protected function run()
    {
        $request = $this->getRequest();
        $response = null;
        $manager = new ScymDirectoryManager();
        if ($request->Name == 'Persons') {
            $person = $manager->getPersonById($request->Value);
            if (empty($person)) {
                $this->addErrorMessage('Person not found for id '.$request->Value);
                return;
            }
            $response = GetFamilyResponse::BuildResponseForPerson($person);
        }
        else if ($request->Name == 'Addresses') {
            $address = $manager->getAddressById($request->Value);
            if (empty($address)) {
                $this->addErrorMessage('Address not found for id  '.$request->Value);
            }
            $response = GetFamilyResponse::BuildResponseForAddress($address);
        }

        $this->setReturnValue($response);
    }


}