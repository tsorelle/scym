<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/23/2015
 * Time: 3:07 PM
 */

namespace App\sys;
use Drupal\Core\Session\AccountInterface;
use Drupal\tops\Identity\TDrupalUser;
use Tops\db\TEntityManagers;
use App\db\scym\ScymPerson;

class ScymUser extends TDrupalUser
{
    private static $profileCache = array();

    protected function test()
    {
        return 'scym';
    }

    /**
     * @param AccountInterface $user
     *
     * Assign firstName, lastName, middleName and pictureFile
     *
     * This version for Drupal 7 - might work with 8, we'll see...
     */
    protected function loadProfile()
    {
        if (array_key_exists($this->userName,self::$profileCache)) {
            $this->profile = self::$profileCache[$this->userName];
        }
        parent::loadProfile();
        $person = $this->getPersonEntity();
        if ($person) {
            $this->setProfileValue('firstName',$person->getFirstname());
            $this->setProfileValue('lastName',$person->getLastname());
            $this->setProfileValue('middleName',$person->getMiddlename());
            $this->setProfileValue('email',$person->getEmail());
            $this->setProfileValue('fullName',$person->getFullName());
            $this->setProfileValue('shortName',$person->getShortName());
        }
        self::$profileCache[$this->userName] = $this->profile;
    }

    /**
     * @return null|ScymPerson
     */
    public function getPersonEntity()
    {
        $name = $this->getUserName();
        if ($name == 'admin') {
            $name = 'tsorelle';
        }

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymPerson');
        $person = $repository->findOneBy(array('username' => $name));
        return $person;
    }
}