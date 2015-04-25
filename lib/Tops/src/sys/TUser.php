<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/11/2015
 * Time: 7:01 AM
 */

namespace Tops\sys;

use Tops\sys\IUserFactory;

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
            self::$currentUser = self::Create();
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
            self::$currentUser = self::Create();
            self::$currentUser->loadByUserName($userName);
        }
        return self::$currentUser;
    }

    public static function getByUserName($userName) {

        $result = self::Create();
        $result->loadByUserName($userName);
        return $result;
    }

    public static function getById($uid) {
        $result = self::Create();
        $result->loadById($uid);
        return $result;
    }

    private static $userFactory;
    public static function setUserFactory(IUserFactory $factory)
    {
        // set factory for testing routines
        self::$userFactory = $factory;
    }

    /**
     * @return IUserFactory
     */
    private static function getUserFactory() {
        if (!isset(self::$userFactory)) {
            self::$userFactory = TObjectContainer::Get('tops.userfactory');
        }
        return self::$userFactory;
    }

    /**
     * @return IUser
     */
    public static function Create() {

        return self::getUserFactory()->createUser();

    }


}