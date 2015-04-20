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
    protected $firstName = '';
    protected $lastName = '';
    protected $middleName = '';
    protected $userName = '';
    protected $fullName = '';
    protected $email = '';

    protected $isCurrentUser = false;

    /**
     * @var array
     */
    protected   $profile = null;



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
     * @internal param $first
     * @internal param string $last
     * @internal param string $middle
     */
    public function concatFullName()
    {
        $this->fullName = $this->firstName;
        if (!empty($this->middleName))
            $this->fullName .= ' ' . $this->middleName;
        if (!empty($this->lastName))
            $this->fullName .= ' ' . $this->lastName;

    }  //  concatFullName

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
        return $this->firstName;
    }  //  getFirstName

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
        if (!empty($this->fullName))
            return $this->fullName;

        $result = '';
        if (!empty($this->firstName)) {
            $result = $this->firstName;
        }
        if (!empty($this->middleName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= $this->middleName;
        }
        if (!empty($this->lastName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= $this->lastName;
        }

        if (empty($result) && $defaultToUsername)
            return $this->userName;

        return $result;
    }  //  getfullName

    /**
     * @param bool $defaultToUsername
     * @return string
     */
    public function getUserShortName($defaultToUsername = true)
    {
        $result = '';
        if (!empty($this->firstName)) {
            $result = $this->firstName; //  substr($this->firstName,0,1).'.';
        }
        if (!empty($this->middleName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= substr($this->middleName, 0, 1) . '.';
        }
        if (!empty($this->lastName)) {
            if (!empty($result))
                $result .= ' ';
            $result .= $this->lastName;
        }

        if (empty($result) && $defaultToUsername)
            $result = $this->userName;

        return $result;
    }  //  getfullName


    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
        if (!isset($this->profile)) {
            $this->loadProfile();
        }
        if (array_key_exists($this->profile,$key)) {
            return $this->profile[$key];
        }
    }

    public function setProfileValue($key,$value) {
        if (!isset($this->profile)) {
            $this->loadProfile();
        }
        if (array_key_exists($this->profile,$key)) {
            $this->profile[$key] = $value;
            $this->updateProfile($key);
        }
    }

    public function updateProfile($key=null) {
        // override in sub-class as needed
    }



}