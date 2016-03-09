<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 2:07 PM
 */

namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymMailbox;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;



class NewPersonForAddressCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $request = $this->getRequest();
        $personDto = $request->person;
        $addressId = $request->addressId;

        if (empty($personDto)) {
            $this->addErrorMessage('Expected person object');
            return;
        }
        if (empty($addressId)) {
            $this->addErrorMessage('Expected address id');
            return;
        }
        $person = new ScymPerson();
        $person->updateFromDataTransferObject($personDto);
        $address = $manager->getAddressById($addressId);
        if ($address == null) {
            $this->addErrorMessage("Address not found.");
        }
        $person->setAddress($address);
        $manager->updateEntity($person);
        $result = $person->getDataTransferObject();
        $this->setReturnValue($result);
    }
}