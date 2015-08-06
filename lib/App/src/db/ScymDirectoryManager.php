<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:16 AM
 */

namespace App\db;


use App\db\scym\ScymAddress;
use App\db\scym\ScymMeeting;
use App\db\scym\ScymPerson;
use Doctrine\ORM\EntityRepository;
use Tops\db\TEntityManagers;
use Tops\sys\TNameValuePair;

class ScymDirectoryManager
{

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $repositories = array();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager() {
        if (!(isset($this->entityManager))) {
            $this->entityManager = TEntityManagers::Get();
        }
        return $this->entityManager;
    }

    /**
     * @param $className
     * @return EntityRepository
     */
    private function getRepository($className) {
        if (!isset($this->repositories[$className])) {
            $em = $this->getEntityManager();
            $repository = $em->getRepository($className);
            $this->repositories[$className] = $repository;
        }
        return $this->repositories[$className];
    }

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
            '(p.firstname LIKE :name OR p.lastname LIKE :name OR p.middlename LIKE :name) ORDER BY p.lastname,p.firstname';
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

}