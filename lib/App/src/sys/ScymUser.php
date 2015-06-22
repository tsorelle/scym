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
use Tops\cache\TSessionCache;
use Tops\db\TEntityManagers;
use App\db\scym\ScymPerson;
use Tops\sys\TTracer;

class ScymUser extends TDrupalUser
{

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
        TTracer::Trace("Load profile for $this->userName");
        parent::loadProfile();

        // May have future integration with SCYM Directory. Not currently used
        /*

        $person = $this->getPersonEntity();
        if ($person) {
            $this->profile['firstName'] = $person->getFirstname();
            $this->profile['lastName'] = $person->getLastname();
            $this->profile['middleName'] = $person->getMiddlename();
            $this->profile['email'] = $person->getEmail();
            if (!isset($this->profile['fullName'])) {
                $this->profile['fullName'] = $person->getFullName();
                $this->profile['shortName'] = $person->getShortName();
            }
            TTracer::Trace("Found ".$person->getUsername());
        }
        else {
            TTracer::Trace("user $this->userName not found.");
        }
        */
    }

    /**
     * @return null|ScymPerson
     */
    public function getPersonEntity()
    {
        $name = $this->userName;
        // $name = $this->getUserName();
        TTracer::Trace("getPersonEntity for name= '$name'" );
        if ($name == 'admin') {
            return null;
        }

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymPerson');
        $person = $repository->findOneBy(array('username' => $name));
        return $person;
    }
}