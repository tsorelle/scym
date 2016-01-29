<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/28/2016
 * Time: 5:21 PM
 */

namespace App\src\services\registration;


use App\db\ScymRegistrationsManager;
use Tops\services\TServiceCommand;
use Tops\sys\IUser;
use Tops\sys\TUser;

class GetSessionInfoCommand extends TServiceCommand
{

    protected function run()
    {
        $manager = new ScymRegistrationsManager();

        $user = TUser::getCurrent();
        $responseData = new \stdClass();
        $sessionInfo = $manager->getSession();
        $responseData->sessionInfo = $sessionInfo->toDataTransferObject();
        $responseData->user = $this->getUserInfo($user);
    }
    private function getUserInfo(IUser $user) {
        $result = new \stdClass();
        $result->authenticated = $user->isAuthenticated();
        $result->id = $user->getId();
        $result->name = $user->getFullName();
        $result->email = $user->getEmail();
        $result->isAdmin = $user->isAdmin();
        $result->isRegistrar = $result->isAdmin || $user->isMemberOf('registrar');
        $result->isYouthProgramStaff = $result->isAdmin || $result->isRegistrar || $user->isMemberOf('youthprogram');
        return $result;
    }

}