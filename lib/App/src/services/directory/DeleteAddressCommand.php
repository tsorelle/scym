<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/10/2015
 * Time: 3:07 PM
 */
namespace App\services\directory;
use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;
use App\services\directory\GetFamilyResponse;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;


class DeleteAddressCommand extends TServiceCommand
{
    public function __construct()
    {
        $this->addAuthorization("administer directory");
    }

    protected function run()
    {
        $manager = new ScymDirectoryManager();
        $addressId = $this->getRequest();
        if (empty($addressId)) {
            $this->addErrorMessage('Expected address id.');
            return;
        }
        $manager->deactivateAddress($addressId);
    }
}