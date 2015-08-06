<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:13 AM
 */

namespace App\services\directory;
use App\db\ScymDirectoryManager;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\TUser;

class InitDirectoryAppCommand extends TServiceCommand
{
    public function __construct() {
        $this->addAuthorization("view directory");
    }


    protected function run()
    {
        $result = new InitDirectoryResponse();
        $manager = new ScymDirectoryManager();
        $user = TUser::getCurrent();

        $result->canEdit = $user->isAuthorized('administer directory');

        $result->affiliationCodes = $manager->getAffiliationCodeList();
        $result->directoryListingTypes = $manager->getDirectoryListingTypeList();

        $this->setReturnValue($result);
    }


}