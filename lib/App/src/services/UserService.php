<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 1/30/2016
 * Time: 6:43 AM
 */

namespace App\services;


use Tops\sys\IUser;
use Tops\sys\TUser;

class UserService
{
    public static function getUserInfo(IUser $user = null) {
        if ($user == null) {
            $user = TUser::getCurrent();
        }
        $result = new \stdClass();
        $result->authenticated = $user->isAuthenticated();
        $result->id =  $result->authenticated ? $user->getId() : 0;
        $result->name =  $result->authenticated ? $user->getFullName() : 'Guest' ;
        $result->email = $result->authenticated ? $user->getEmail() : '';
        $result->isAdmin = $result->authenticated ? $user->isAdmin() : false;
        $result->authorizations=array();
        if ($result->authenticated) {
            if ($user->isAuthorized('manage mailboxes'))  { array_push($result->authorizations,'mailboxes'); }
            if ($user->isAuthorized('administer registrations'))  { array_push($result->authorizations,'registrar'); }
            if ($user->isAuthorized('administer directory'))  { array_push($result->authorizations,'directory'); }
            if ($user->isAuthorized('administer meeting directory'))  { array_push($result->authorizations,'meetings'); }
            if ($user->isAuthorized('export directory'))  { array_push($result->authorizations,'export'); }
            if ($user->isAuthorized('administer housing'))  { array_push($result->authorizations,'housing'); }
            if ($user->isAuthorized('administer youth'))  { array_push($result->authorizations,'youth'); }
        }
        return $result;
    }

}