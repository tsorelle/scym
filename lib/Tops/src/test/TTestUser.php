<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/10/2015
 * Time: 10:33 AM
 */

namespace Tops\test;


use Tops\sys\TAbstractUser;
use Tops\sys\TCollection;
use Tops\sys\TUser;

class TTestUser extends TAbstractUser {

    /**
     * @var TCollection
     */
    private static $userList;
    private static $authorizations;


    private static $currentUserName;

    /**
     * @var string[]
     */
    private $roles;


    public function __construct($userId=null)
    {
        if ($userId === null) {
            $this->id = 0;
            $this->userName = 'guest';
            $this->roles = array("anonymous");
        }
        else if (is_numeric($userId)) {
            if ($userId == 0) {
                $this->roles = array("admin");
                if (!$this->loadById(1)) {
                    $this->userName = 'admin';
                }
            }
            else {
                $this->loadById($userId);
            }
        }
        else if ($userId == '~current') {
            $this->loadCurrentUser();
        } else {
            $this->loadByUserName($userId);
        }
    }

    /**
     * @return TCollection
     */
    public static function getUserList()
    {
        if (!isset(self::$userList)) {
            self::$userList = new TCollection();
        }
        return self::$userList;
    }

    /**
     * @return TCollection
     */
    public static function getAuthorizationList()
    {
        if (!isset(self::$authorizations)) {
            self::$authorizations = new TCollection();
        }
        return self::$authorizations;
    }

    public static function setCurrentUserName($userName) {
        self::$currentUserName = $userName;
        TUser::getCurrent()->loadCurrentUser();
    }
    public static function addUser($userName,$id,$roles,$firstName = '',$lastName='',$email='')
    {
        if (!isset(self::$currentUserName)) {
            self::$currentUserName = $userName;
        }

        $user = new \stdClass();
        $user->userName = $userName;
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->id = $id;
        $user->roles = explode(',', $roles);
        $user->email = $email;
        self::getUserList()->set($userName,$user);
    }

    public static function addAuthorization($roleName,$authName) {
        $auth = new \stdClass();
        $auth->role = $roleName;
        $auth->auth = $authName;
        self::getAuthorizationList()->add($auth);
    }



    private function loadUser($user) {
        $this->userName = $user->userName;
        $this->firstName = $user->firstName;
        $this->lastName = $user->lastName;
        $this->id = $user->id;
        $this->roles = $user->roles;
        $this->email = $user->email;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function loadById($id)
    {
        $user = self::getUserList()->first(
            function ($user, $idValue) {
                return $user->id == $idValue;
            }, $id);

        if (empty($user)) {
            return false;
        }
        $this->loadUser($user);
        return true;
    }

    /**
     * @param $userName
     * @return mixed
     */
    public function loadByUserName($userName)
    {
        $user = self::getUserList()->get($userName);
        if (empty($user)) {
            return false;
        }
        $this->loadUser($user);
        return true;
    }

    /**
     * @return mixed
     */
    public function loadCurrentUser()
    {
        if (empty(self::$currentUserName)) {
            self::$currentUserName = 'testuser';
            self::addUser('testuser',1,'admin','Test','User','test@user.com');
        }
        $this->setCurrent();
        return $this->loadByUserName(self::$currentUserName);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isMemberOf('admin');
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param $roleName
     * @return bool
     */
    public function isMemberOf($roleName)
    {
        return (!empty($this->roles) && in_array($roleName,$this->roles));
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isAuthorized($value = '')
    {
        if ($this->isAdmin()) {
            return true;
        }
        $searchValue = new \stdClass();
        $searchValue->auth = $value;

        foreach ($this->roles as $role) {
            $searchValue->role = $role;
            $result = self::getAuthorizationList()->first (
                function ($auth, $searchValue) {
                    return ($auth->role == $searchValue->role &&
                        $auth->auth == $searchValue->auth);
                }, $searchValue);

            if ($result !== null) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->userName != 'guest';
    }

    protected function loadProfile()
    {
        $this->profile = array();
    }
}