<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/28/2016
 * Time: 5:21 PM
 */

namespace App\services\registration;


use App\db\ScymRegistrationsManager;
use App\services\UserService;
use Tops\services\TServiceCommand;
use Tops\sys\IUser;
use Tops\sys\TUser;

class GetSessionInfoCommand extends TServiceCommand
{

    protected function run()
    {
        $manager = new ScymRegistrationsManager();
        $responseData = new \stdClass();
        $sessionInfo = $manager->getSession();
        $responseData->sessionInfo = $sessionInfo->toDataTransferObject();
        $responseData->user = UserService::getUserInfo();
        $this->setReturnValue($responseData);
    }
}