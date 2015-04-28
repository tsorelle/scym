<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2/10/2015
 * Time: 8:22 AM
 */

namespace Tops\sys;


abstract class TAbstractUser implements IUser
{


    protected $id = 0;
    protected $userName = '';
    protected $isCurrentUser = false;

    /**
     * @var array
     */
    protected   $profile = null;

    protected abstract function test();

    /**
     * @param $id
     * @return mixed
     */
    public abstract function loadById($id);

    /**
     * @param $userName
     * @return mixed
     */
    public abstract function loadByUserName($userName);

    /**
     * @return mixed
     */
    public abstract function loadCurrentUser();

    /**
     * @return bool
     */
    public abstract function isAdmin();

    /**
     * @return string[]
     */
    public abstract function getRoles();

    /**
     * @param $roleName
     * @return bool
     */
    public abstract function isMemberOf($roleName);

    /**
     * @param string $value
     * @return bool
     */
    public abstract function isAuthorized($value = '');

    /**
     * @return bool
     */
    public abstract function isAuthenticated();

    protected abstract function loadProfile();

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }  //  getId

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getProfileValue('firstName');
    }  //  getFirstName

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getProfileValue('lastName');

    }  //  getLastName

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }  //  getUserName

    /**
     * @param bool $defaultToUsername
     * @return string
     */
    public function getFullName($defaultToUsername = true)
    {
        if ($this->userName == 'admin') {
            return "The administrator";
        }

        $name = $this->getProfileValue('fullName');

        if (empty($name)) {
            if ($defaultToUsername) {
                return $this->userName;
            }
            return '';
        }
        return $name;
    }  //  getfullName

    /**
     * @param bool $defaultToUsername
     * @return string
     */
    public function getUserShortName($defaultToUsername = true)
    {

        TTracer::Trace("Get short name for $this->userName");
        $name = $this->getProfileValue('shortName');
        if (empty($name)) {
            return $this->getFullName($defaultToUsername);
        }
        return $name;

    }  //  getfullName


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getProfileValue('email');
    }  //  getEmail

    public function isCurrent()
    {
        return $this->isCurrentUser;
    }

    protected function setCurrent()
    {
        $this->isCurrentUser = true;
    }

    public function getProfileValue($key) {
        TTracer::Trace("getProfileValue($key) for $this->userName");
        if (!isset($this->profile)) {
            $userName = $this->getUserName();
            if (empty($userName)) {
                return null;
            }

            $this->loadProfile();
        }

        if (array_key_exists($key,$this->profile)) {
            TTracer::Trace("getProfileValue($key) key exists for $this->userName");
            return $this->profile[$key];
        }
        return '';
    }



    public function setProfileValue($key,$value) {
        if (!isset($this->profile)) {
            $this->loadProfile();
        }
        $isUpdate = array_key_exists($key,$this->profile) ;
        $this->profile[$key] = $value;
        if ($isUpdate) {
            $this->updateProfile($key);
        }
    }

    public function updateProfile($key=null) {
        // override in sub-class as needed
    }



}