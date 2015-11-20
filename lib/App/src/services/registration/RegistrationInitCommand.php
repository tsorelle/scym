<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/17/2015
 * Time: 4:41 PM
 */
namespace App\services\registration;

use App\db\ScymRegistrationsManager;
use Tops\services;
use Tops\services\TServiceCommand;
use Tops\sys\IUser;
use Tops\sys\TUser;

class RegistrationInitCommand extends TServiceCommand
{
    /**
     * Service contract
     *
     * export interface IUser {
     *      id:string;
     *      name:string;
     *      authenticated : number;
     *      email:string;
     *  }
     *  export interface IRegistrationUser extends  IUser {
     *      isRegistrar : number;
     *      registrationId: any;
     *  }
     *  export interface IAnnualSessionInfo {
     *     year : string;
     *     startDate : any;
     *     endDate : any;
     *     datesText : string;
     *     location : string
     *  }
     *  export interface IRegistrationInitResponse {
     *      sessionInfo : IAnnualSessionInfo;
     *      user : IRegistrationUser;
     *      registrationId : any;
     *  }
     */

    protected function run()
    {
        $manager = new ScymRegistrationsManager();
        $user = TUser::getCurrent();
        $responseData = new \stdClass();
        $sessionInfo = $manager->getSession();
        $responseData->sessionInfo = $sessionInfo->toDataTransferObject();
        $responseData->user = $this->getUserInfo($user);
        $responseData->user->registrationId = $manager->getUserRegistrationId($user->getUserName(),$sessionInfo->getYear());
        $this->setReturnValue($responseData);
    }

    private function getUserInfo(IUser $user) {
        $result = new \stdClass();
        $result->authenticated = $user->isAuthenticated();
        $result->id = $user->getId();
        $result->name = $user->getFullName();
        $result->email = $user->getEmail();
        $result->isRegistrar = $user->isAdmin() || $user->isMemberOf('registrar');
        return $result;
    }
}