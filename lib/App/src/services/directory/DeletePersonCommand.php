<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/10/2015
 * Time: 2:16 PM
 */

namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;


class DeletePersonCommand extends TServiceCommand
{
    public function __construct()
    {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $personId = $this->getRequest();
        if (empty($personId)) {
            $this->addErrorMessage('Expected person id.');
            return;
        }
        $manager->deactivatePerson($personId);
    }
}