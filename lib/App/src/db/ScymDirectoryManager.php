<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:16 AM
 */

namespace App\db;


use App\db\scym\ScymAddress;
use App\db\scym\ScymPerson;
use Doctrine\ORM\EntityRepository;
use Tops\db\TDbServiceManager;
use Tops\sys\TNameValuePair;

class ScymDirectoryManager extends TDbServiceManager
{
    /**
     * @return EntityRepository
     */
    private function getPersonsRepository() {
        return $this->getRepository('App\db\scym\ScymPerson');
    }

    /**
     * @return EntityRepository
     */
    private function getAddressesRepository() {
        return $this->getRepository('App\db\scym\ScymAddress');
    }

    /**
     * @return \App\db\scym\ScymMeeting[]
     */
    private function getMeetings() {
        $repository = $this->getRepository('App\db\scym\ScymMeeting');
        $meetings = $repository->findByActive(1);
        return $meetings;
    }

    /**
     * @return \App\db\scym\ScymDirectoryListingType[];
     */
    private function getDirectoryListingTypes() {
        $repository = $this->getRepository('App\db\scym\ScymDirectoryListingType');
        $result = $repository->findAll();
        return $result;
    }

    public function getAffiliationCodeList()
    {
        $result = array();
        $meetings = $this->getMeetings();
        foreach ($meetings as $meeting) {
            TNameValuePair::AddToArray($result,$meeting->getMeetingname(),$meeting->getAffiliationcode());
        }


        return $result;

    }

    public function getDirectoryListingTypeList() {
        $result = array();
        $types = $this->getDirectoryListingTypes();
        foreach($types as $type) {
            TNameValuePair::AddToArray($result,$type->getDescription(),$type->getListingtypeid());
        }
        return $result;
    }

    /**
     * @param $searchString
     * @return ScymPerson[]
     */
    private function searchForPersons($searchString){
        $em = $this->getEntityManager();
        $sql = 'SELECT p FROM App\db\scym\ScymPerson p WHERE p.active=1 AND '.
            '(p.firstname LIKE :name OR p.lastname LIKE :name OR p.middlename LIKE :name '.
            "OR CONCAT(p.firstname,' ',p.lastname) like :name ".
            "OR CONCAT(p.firstname,' ',p.middlename,' ',p.lastname) like :name) ".
            'ORDER BY p.lastname,p.firstname';
        $query = $em->createQuery($sql);
        $query->setParameter('name', '%'.$searchString.'%');
        $result = $query->getResult();
        return $result;
    }

    public function getPersonList($searchString) {
        $result = array();
        $persons = $this->searchForPersons($searchString);
        foreach($persons as $person) {
            TNameValuePair::AddToArray($result,$person->getFullName(),$person->getPersonid());
        }
        return $result;
    }

    /**
     * @param $searchString
     * @return ScymAddress[]
     */
    private function searchForAddresses($searchString) {
        $em = $this->getEntityManager();
        $sql = 'SELECT a FROM App\db\scym\ScymAddress a WHERE a.active=1 AND a.addressname LIKE :name ORDER BY a.addressname';
        $query = $em->createQuery($sql);
        $query->setParameter('name', '%'.$searchString.'%');
        $result = $query->getResult();
        return $result;
    }

    public function getAddressList($searchString) {
        $result = array();
        $addresses = $this->searchForAddresses($searchString);
        foreach($addresses as $address) {
            TNameValuePair::AddToArray($result,$address->getAddressname(),$address->getAddressid());
        }
        return $result;
    }

    /**
     * @param $id
     * @return null|ScymPerson
     */
    public function getPersonById($id) {
        $persons = $this->getPersonsRepository();
        return $persons->find($id);
    }

    /**
     * @param $id
     * @return null|ScymAddress
     */
    public function getAddressById($id) {
        $addresses = $this->getAddressesRepository();
        return $addresses->find($id);
    }

    public function addPersonToAddress(ScymPerson $person, $addressId) {
        $address = $this->getAddressById($addressId);
        if ($address) {
            $this->joinPersonAndAddress($person,$address);
        }
        return $address;
    }

    public function joinPersonAndAddress(ScymPerson $person, ScymAddress $address) {
        $person->setAddress($address);
        $this->updateEntity($person);
        // must call refresh to update the 1:m side of the relationship in memory.
        $this->entityManager->refresh($address);
    }

    public function removePersonAddress(ScymPerson $person) {
        $address = null;
        if ($person) {
            $address = $person->getAddress();
            if ($address) {
                $person->setAddress(null);
                $this->updateEntity($person);
                // must call refresh to update the 1:m side of the relationship in memory.
                $this->entityManager->refresh($address);
            }
        }
    }

    public function deactivatePerson($personId) {
        $person = $this->getPersonById($personId);
        $person->setActive(0);
        $person->setAddress(null);
        $this->updateEntity($person);
    }

    public function deactivateAddress($addressId) {
        $em = $this->getEntityManager();
        $address = $this->getAddressById($addressId);
        if ($address != null) {
            $people = $address->getPersons();
            foreach ($people as $person ) {
                $person->setAddress(null);
                $em->persist($person);
            }
        }
        $em->flush();
        $this->entityManager->refresh($address);
        $address->setActive(0);
        $this->updateEntity($address);
    }

    public function getEmailForNewsletter() {
        $repository = $this->getPersonsRepository();
        $persons = $repository->findBy(array('active'=>1,'newsletter'=>1),array('email'=>'ASC'));
        $result = array();
        $rec = "\"First Name\",\"Last Name\",\"Email Address\"\n";
        array_push($result,$rec);
        $previousEmail = null;
        foreach ($persons as $p) {
            $email = $p->getEmail();

            if (!empty($email)) {
                if ($email == $previousEmail) {
                    continue; // skip duplicate addresses
                }
                $previousEmail = $email;
                $first = $p->getFirstName();
                $last = $p->getLastName();
                $rec = "\"".$p->getFirstName()."\",\"".$p->getLastName()."\",\"".$email."\"\n";
                array_push($result,$rec);
            }
        }
        return $result;
    }

    /**
     * @param $address
     * @return ScymPerson[]
     */
    public function getPersonByEmail($address) {
        $repository = $this->getPersonsRepository();
        $persons = $repository->findBy(array('email'=>$address));
        return $persons;
    }


}