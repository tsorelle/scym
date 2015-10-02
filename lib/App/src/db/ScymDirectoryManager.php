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
use App\db\scym\ScymEmailBounce;
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

    /**
     * @return \App\db\scym\ScymMembershipType[];
     */
    private function getMembershipTypes() {
        $repository = $this->getRepository('App\db\scym\ScymMembershipType');
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

    public function getMembershipTypeList() {
        $result = array();
        $types = $this->getMembershipTypes();
        foreach($types as $type) {
            if ($type->getActive()) {
                TNameValuePair::AddToArray($result, $type->getMembershiptypename(), $type->getMembershiptypeid());
            }
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

    const addressDownloadHeader = '"addressName","address1","address2","city","state","zip","country"';

    private function getAddressDownloadRecord(ScymAddress $address, $excludeEmpty = true) {
        $name = $address->getAddressname();
        $addr1 = $address->getAddress1();
        $addr2 = $address->getAddress2();

        if   ($excludeEmpty && (empty($name) || (empty($addr1) && empty($addr2)))) {
            return null;
        }

        $city = $address->getCity();
        $state = $address->getState();
        $zip = $address->getPostalcode();
        $country = $address->getCountry();
        return 
            '"'.($name? $name : '').'",'.
            '"'.($addr1 ?  $addr1 : '').'",'.
            '"'.($addr2 ? $addr2 : '').'",'.
            '"'.($city? $city : '').'",'.
            '"'.($state? $state : '').'",'.
            '"'.($zip ? $zip : '').'",'.
            '"'.($country?  $country: '').'"';
    }

    public function getAddressListForDownload($directoryOnly=false,$residenceOnly=false,$newsletter=false) {
        $whereClause = '';
        if ($directoryOnly) {
            $whereClause = 'a.directoryListingTypeId=1 ';
        }
        if ($residenceOnly) {
            $whereClause = $whereClause. ($whereClause ? ' AND ' : '').'a.addresstype=1';
        }
        if ($newsletter) {
            $whereClause = $whereClause. ($whereClause ? ' AND ' : '').'a.newsletter=1';
        }

        if ($whereClause) {
            $whereClause = ' AND ('.$whereClause.') ';
        }
        $sql = 'SELECT a FROM App\db\scym\ScymAddress a WHERE a.active=1 '.$whereClause.' ORDER BY a.addressname';
        $result = array();
        array_push($result,self::addressDownloadHeader."\n");
        $em = $this->getEntityManager();
        $query = $em->createQuery($sql);
        $addresses = $query->getResult();
        foreach($addresses as $address) {
            $rec = $this->getAddressDownloadRecord($address);
            if (!empty($rec)) {
                array_push($result, "$rec\n");
            }
        }
        return $result;
    }
    
    private function getContactDownloadRecord(ScymPerson $person)
    {
        $firstName        = $person->getFirstname();
        $lastName         = $person->getLastname();
        $middleName       = $person->getMiddlename();
        $phone1             = $person->getPhone();
        $phone2             = $person->getPhone2();
        $email            = $person->getEmail();
        $dob            = $person->getDateOfBirth();

        if ($dob == null) {
            $dob = '';
        }
        else {
            $dob = $dob->format('Y-m-d');
        }

        $junior           = $person->getJunior();
        $affiliationCode  = $person->getAffiliationcode();

        $rec =
            '"'.($firstName ? $firstName : '').'",'.
            '"'.($lastName ? $lastName : '').'",'.
            '"'.($middleName ? $middleName : '').'",'.
            '"'.($phone1 ? $phone1 : '').'",'.
            '"'.($phone2 ? $phone2 : '').'",'.
            '"'.($email ? $email : '').'",'.
            '"'.$dob.'",'.
            '"'.($junior ? 1 : 0).'",'.
            '"'.($affiliationCode ?  $affiliationCode : '').'",';
        $address = $person->getAddress();
        if ($address == null) {
            $rec .= '"","","","","","",""';
        }
        else {
            $addressRec = $this->getAddressDownloadRecord($address);
            $rec .= $addressRec;
        }
        $householdPhone = '';
        if ($address) {
            $phone   = $address->getPhone();
            if (!empty($householdPhone)) {
                $householdPhone = $phone;
            }
        };
        $rec .= ',"'.$householdPhone.'"';
        return $rec;
    }

    public function getContactListForDownload($directoryOnly=false,$includeKids=false,$affiliationCode=null) {
        $whereClause = '';
        if ($directoryOnly) {
            $whereClause = 'p.directoryListingTypeId=1 ';
        }
        if (!$includeKids) {
            $whereClause = $whereClause. ($whereClause ? ' AND ' : '').'p.junior <> 1';
        }
        if ($affiliationCode) {
            $whereClause = $whereClause. ($whereClause ? ' AND ' : '')."p.affiliationcode='$affiliationCode'";
        }
        if ($whereClause) {
            $whereClause = ' AND ('.$whereClause.') ';
        }
        $em = $this->getEntityManager();
        $result = array();
        $header = '"firstName","lastName","middleName","phone1","phone2","email","dateOfBirth","junior","affiliation",'.self::addressDownloadHeader.',"householdPhone"'."\n";
        array_push($result,$header);
        $sql = 'SELECT p FROM App\db\scym\ScymPerson p WHERE p.active=1 '.$whereClause.' ORDER BY p.firstname,p.lastname';
        $query = $em->createQuery($sql);
        $persons = $query->getResult();
        foreach($persons as $person) {
            $rec = $this->getContactDownloadRecord($person);
            if (!empty($rec)) {
                array_push($result, "$rec\n");
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

    public function getEmailBounces()
    {
        $repository = $this->getRepository('App\db\scym\ScymEmailBounce');
        $bounces = $repository->findAll();
        $result = array();
        foreach ($bounces as $bounce) {
            array_push($result, $bounce->getDataTransferObject());
        }
        return $result;
    }

    /**
     * @param $repository
     * @param $email
     * @return ScymEmailBounce | null
     */
    public function getEmailBounce(EntityRepository $repository, $email) {
        $bounces = $repository->findBy(array('email' => $email));
        if (empty($bounces)) {
            return null;
        }
        return $bounces[0];
    }

    public function resolveEmailBounces(array $updates)
    {
        $em = $this->getEntityManager();

        $repository = $this->getRepository('App\db\scym\ScymEmailBounce');
        foreach ($updates as $update) {
            $bounce = $this->getEmailBounce($repository,$update->email);
            if ($bounce) {
                if ($update->correction) {
                    $person = $bounce->getPerson();
                    $person->setNewsletter(1);
                    $person->setEmail($update->email);
                }
                $em->remove($bounce);
            }
        }
        $this->saveChanges();
        return $this->getEmailBounces();
    }

    public function addEmailBounces($bounces) {
        $em = $this->getEntityManager();
        $repository = $this->getRepository('App\db\scym\ScymEmailBounce');
        foreach ($bounces as $bounce) {
            $entity = $this->getEmailBounce($repository,$bounce->email);
            if ($entity == null) {
                $entity = new ScymEmailBounce();
                $entity->setEmail($bounce->email);
                $entity->setPerson($bounce->person);
                $em->persist($entity);
            }
        }
    }

    public function processBounceCorrections($bounces) {
        $updateCount = 0;
        $em = $this->getEntityManager();
        $bouceRepository = $this->getRepository('App\db\scym\ScymEmailBounce');
        foreach($bounces as $bounceItem) {
            $bounce = $this->getEmailBounce($bouceRepository,$bounceItem->email);
            if ($bounce) {
                $updateCount++;
                if ($bounceItem->correction && !$bounceItem->remove) {
                    $person = $bounce->getPerson();
                    if ($person) {
                        $person->setEmail($bounceItem->correction);
                        $em->persist($person);
                    }
                }
                $em->remove($bounce);
            }
        }
        $this->saveChanges();
        return $updateCount;
    }

    public function processEmailCorrections($corrections) {
        $updateCount = 0;
        $em = $this->getEntityManager();
        $bouceRepository = $this->getRepository('App\db\scym\ScymEmailBounce');
        foreach($corrections as $correction) {
            $address = trim($correction->address);
            $status = trim($correction->status);
            if ((!empty($address)) && (!empty($status))) {
                $persons = $this->getPersonByEmail($address);
                foreach($persons as $person) {
                    $updateCount++;
                    $bounceEntity = null;
                    $person->setNewsletter(0);
                    if ($status != 'unsubscribed') {
                        $bounceEntity = new ScymEmailBounce();
                        $person->setEmail('');
                    }
                    $em->persist($person);
                    if ($bounceEntity) {
                        if (!$this->getEmailBounce($bouceRepository,$address)) {
                            $bounceEntity->setEmail($address);
                            $bounceEntity->setPerson($person);
                            $em->persist($bounceEntity);
                        }
                    }
                }
            }
        }
        if ($updateCount) {
            $this->saveChanges();
        }
        return $updateCount;
    }

}