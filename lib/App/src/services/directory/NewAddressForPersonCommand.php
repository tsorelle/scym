<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/10/2015
 * Time: 1:42 PM
 */
namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;




class NewAddressForPersonCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $request = $this->getRequest();
        $personId = $request->personId;
        $addressDto = $request->address;
        if (empty($personId)) {
            $this->addErrorMessage('Expected person id.');
        }
        if ($addressDto == null) {
            $this->addErrorMessage('Expected address object');
            return;
        }

        $person = $manager->getPersonById($personId);
        $address = new Scymaddress();
        $address->updateFromDataTransferObject($addressDto);
        $manager->updateEntity($address);
        $manager->joinPersonAndAddress($person,$address);

        $result = GetFamilyResponse::BuildResponseForAddress($address);
        $this->setReturnValue($result);

    }
}