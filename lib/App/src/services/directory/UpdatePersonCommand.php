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



class UpdatePersonCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("administer directory");
    }



    protected function run()
    {
        $request = $this->getRequest();

        $manager = new ScymDirectoryManager();
        $id = $request->personId;
        $person = null;
        if (empty($id)) {
            $person = new ScymPerson();
        }
        else {
            $person = $manager->getPersonById($id);
            if (empty($person)) {
                $this->addErrorMessage('Person not found for id ' . $request->Value);
                return;
            }

        }


        $valid = $person->updateFromDataTransferObject($request);
        if (!$valid) {
            $this->addErrorMessage('Cannot update person entity due to invalid data.');
        }

        $manager->updateEntity($person);
        $result = $person->getDataTransferObject();
        $this->setReturnValue($person);
    }
}