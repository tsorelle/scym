<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/6/2015
 * Time: 8:42 AM
 */

namespace App\services\directory;
use App\db\ScymDirectoryManager;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;

class DirectorySearchCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("view directory");
    }

    protected function run()
    {
        $searchRequest = $this->getRequest();
        $result = array();
        $manager = new ScymDirectoryManager();
        if ($searchRequest->Name == 'Persons') {
            $result = $manager->getPersonList($searchRequest->Value);
        }
        else if ($searchRequest->Name == 'Addresses') {
            $result = $manager->getAddressList($searchRequest->Value);
        }

        $this->setReturnValue($result);
        if (empty($result)) {
            $this->addErrorMessage('No '.strtolower($searchRequest->Name).' found.');
        }

    }
}