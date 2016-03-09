<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 2:07 PM
 */

namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;



class AddPersonToAddressCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $request = $this->getRequest();
        $personId = $request->personId;
        $addressId = $request->addressId;
        $person = $manager->getPersonById($personId);
        $address = null;
        if ($addressId != null) {
            $address = $manager->getAddressById($addressId);
        }

        $person->setAddress($address);
        $manager->updateEntity($person);
        $result = $person->getDataTransferObject();
        $this->setReturnValue($result);

    }
}