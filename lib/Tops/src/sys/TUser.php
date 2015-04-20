<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/11/2015
 * Time: 7:01 AM
 */

namespace Tops\sys;


class TUser {
    /**
     * @var IUser
     */
    private static $currentUser;

    /**
     * @return IUser
     */
    public static function getCurrent() {
        if (!isset(self::$currentUser)) {
            self::$currentUser = TObjectContainer::Get('tops.user');
            self::$currentUser->loadCurrentUser();
        }
        return self::$currentUser;
    }

    /**
     * @param IUser $user
     */
    public static function setCurrentUser(IUser $user) {
        self::$currentUser = $user;
    }

    public static function setCurrent($userName)
    {
        if (!(isset(self::$currentUser) && self::$currentUser->getUserName() == $userName)) {
            self::$currentUser = TObjectContainer::Get('user');
            self::$currentUser->loadByUserName($userName);
        }
        return self::$currentUser;
    }


}