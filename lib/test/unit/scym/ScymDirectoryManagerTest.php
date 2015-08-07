<?php
use App\db\scym\ScymPerson;
use App\db\ScymDirectoryManager;

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/5/2015
 * Time: 8:45 AM
 */


class ScymDirectoryManagerTest extends \PHPUnit_Framework_TestCase
{
    private $manager;

    private function getDirectoryManager()
    {
        if (!isset( $this->manager)) {
            \Tops\sys\TObjectContainer::Clear();
            \Tops\sys\TObjectContainer::Register('configManager', '\Tops\sys\TYmlConfigManager');
            $this->manager = new ScymDirectoryManager();
        }
        return $this->manager;

    }

    public function testGetAffiliationCodes() {
        $manager = $this->getDirectoryManager();
        $actual = $manager->getAffiliationCodeList();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }
    public function testGetDirectoryListingTypes() {
        $manager = $this->getDirectoryManager();
        $actual = $manager->getDirectoryListingTypeList();
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetPersonList() {
        $manager = $this->getDirectoryManager();
        $actual = $manager->getPersonList('terr');
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }
    public function testGetAddressList() {
        $manager = $this->getDirectoryManager();
        $actual = $manager->getAddressList('terr');
        $this->assertNotNull($actual);
        $this->assertNotEmpty($actual);
    }

    public function testGetPersonById() {
        $testPersonId = 180;
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $actual = $manager->getPersonById($testPersonId);
        $this->assertNotNull($actual);
        $address = $actual->getAddress();
        $this->assertNotNull($address);
        $this->assertEquals($testAddressId,$address->getAddressid());
        $persons = $address->getPersons();
        $found = false;
        foreach($persons as $person) {
            if ($person->getPersonid() == $actual->getPersonid()) {
                $found = true;
            }
        }
        $this->assertTrue($found,'Back reference to person not found.');

    }

    public function testGetAddressById() {
        $testPersonId = 180;
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $actual = $manager->getAddressById($testAddressId);
        $this->assertNotNull($actual);
        $persons = $actual->getPersons();
        $this->assertNotEmpty($persons);
        $found = false;
        foreach($persons as $person) {
            if ($person->getPersonid() == $testPersonId) {
                $found = true;
            }
        }
        $this->assertTrue($found,'Back reference to person not found.');

    }

    private function findPersonInFamilyDto($family,$personId) {
        foreach($family->persons as $personDto) {
            if ($personDto->personId == $personId) {
                return true;
            }
        }
        return false;
    }

    public function testGetFamilyResponseForPerson() {
        $testPersonId = 180;
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $person = $manager->getPersonById($testPersonId);
        $this->assertNotNull($person);
        $actual = \App\services\directory\GetFamilyResponse::BuildResponseForPerson($person);
        $this->assertNotNull($actual);
        $this->assertNotNull($actual->address);
        $this->assertEquals($testAddressId, $actual->address->addressId);
        $this->assertNotEmpty($actual->persons);
        $found = $this->findPersonInFamilyDto($actual,$testPersonId);
        $this->assertTrue($found,'Person not found in DTO');
    }


    public function testGetFamilyResponseForPersonNoAddress() {
        $testPersonId = 289;
        $manager = $this->getDirectoryManager();
        $person = $manager->getPersonById($testPersonId);
        $this->assertNotNull($person);
        $actual = \App\services\directory\GetFamilyResponse::BuildResponseForPerson($person);
        $this->assertNotNull($actual);
        $this->assertNull($actual->address);
        $this->assertNotEmpty($actual->persons);
        $found = $this->findPersonInFamilyDto($actual,$testPersonId);
        $this->assertTrue($found,'Person not found in DTO');
    }


    public function testGetFamilyResponseForAddress() {
        $testPersonId = 180;
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $address = $manager->getAddressById($testAddressId);
        $this->assertNotNull($address);
        $actual = \App\services\directory\GetFamilyResponse::BuildResponseForAddress($address);
        $this->assertNotNull($actual);
        $this->assertNotNull($actual->address);
        $this->assertEquals($testAddressId, $actual->address->addressId);
        $this->assertNotEmpty($actual->persons);
        $found = $this->findPersonInFamilyDto($actual,$testPersonId);
        $this->assertTrue($found,'Person not found in DTO');

    }

    public function testUpdatePerson() {
        $testPersonId = 180;
        $manager = $this->getDirectoryManager();
        $person = $manager->getPersonById($testPersonId);
        $this->assertNotNull($person);
        $email = $person->getEmail();
        $dto = $person->getDataTransferObject();
        $expected = 'e@mail.com';
        $changeDate = $person->getDateUpdated();
        $dto->email = $expected;
        $valid = $person->updateFromDataTransferObject($dto);
        $this->assertTrue($valid,'Invalid date');
        $manager->updateEntity($person);
        $this->assertNotEquals($changeDate,$person->getDateUpdated());
        $person = $manager->getPersonById($testPersonId);
        $this->assertEquals($expected,$person->getEmail());
        $person->setEmail($email);
        $manager->updateEntity($person);
    }

    public function testUpdateAddress() {
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $address = $manager->getAddressById($testAddressId);
        $this->assertNotNull($address);
        $dto = $address->getDataTransferObject();
        $expected = 'testvalue';
        $changeDate = $address->getDateUpdated();
        $sortkey = $address->getSortkey();
        $dto->sortkey = $expected;
        $address->updateFromDataTransferObject($dto);
        $manager->updateEntity($address);
        $this->assertNotEquals($changeDate,$address->getDateUpdated());
        $address = $manager->getAddressById($testAddressId);
        $this->assertEquals($expected,$address->getSortkey());
        $address->setSortkey($sortkey);
        $manager->updateEntity($address);
    }

    public function testInsertAddress() {
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();
        $address = $manager->getAddressById($testAddressId);
        $this->assertNotNull($address);
        $dto = $address->getDataTransferObject();
        $dto->addressname = 'Delete this address';
        $newaddress = new \App\db\scym\ScymAddress();
        $newaddress->updateFromDataTransferObject($dto);
        $manager->updateEntity($newaddress);
        $id = $newaddress->getAddressid();
        $this->assertNotEmpty($id);
        $address = $manager->getAddressById($id);
        $this->assertNotNull($address);
        $this->assertEquals($dto->addressname,$address->getAddressname());
        $manager->deleteEntity($address);
        $address = $manager->getaddressById($id);
        $this->assertNull($address);
    }


    public function testInsertPerson() {
        $testPersonId = 180;
        $manager = $this->getDirectoryManager();
        $person = $manager->getPersonById($testPersonId);
        $this->assertNotNull($person);
        $dto = $person->getDataTransferObject();
        $dto->firstName = 'Delete';
        $dto->lastName = 'Me';
        $newPerson = new \App\db\scym\ScymPerson();
        $newPerson->updateFromDataTransferObject($dto);
        $manager->updateEntity($newPerson);
        $id = $newPerson->getPersonid();
        $this->assertNotEmpty($id);
        $person = $manager->getPersonById($id);
        $this->assertNotNull($person);
        $this->assertEquals($dto->firstName,$person->getFirstname());
        $this->assertEquals($dto->lastName,$person->getLastname());
        $manager->deleteEntity($person);
        $person = $manager->getPersonById($id);
        $this->assertNull($person);
    }

    public function testAddPersonToAddress() {
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();

        $testPerson = new ScymPerson();
        $testPerson->setFirstname('Delete');
        $testPerson->setLastname('Me');
        $manager->updateEntity($testPerson);

        $testId = $testPerson->getPersonid();
        $this->assertNotNull($testId);
        $address = $manager->addPersonToAddress($testPerson,$testAddressId);
        $this->assertNotNull($address);
        $persons = $address->getPersons();
        $personCount = $persons->count();
        $found = false;
        foreach ($persons as $person) {
            if ($person->getPersonid() == $testId) {
                $found = true;
                break;
            }
        }
        $manager->deleteEntity($testPerson);
        $this->assertEquals(3,$personCount);
        $this->assertTrue($found);
    }

    public function testRemovePersonFromAddress() {
        $testAddressId = 117;
        $manager = $this->getDirectoryManager();

        // add a test person
        $testPerson = new ScymPerson();
        $testPerson->setFirstname('Delete');
        $testPerson->setLastname('Me');
        $manager->updateEntity($testPerson);
        $testId = $testPerson->getPersonid();
        $this->assertNotNull($testId);
        $address = $manager->addPersonToAddress($testPerson,$testAddressId);
        $this->assertNotNull($address);
        $persons = $address->getPersons();
        $personCount1 = $persons->count();

        $address = $manager->removePersonAddress($testPerson);

        $persons = $address->getPersons();
        $personCount2 = $persons->count();
        $removedPersons = $personCount1 - $personCount2 ;
        $found = false;
        foreach ($persons as $person) {
            if ($person->getPersonid() == $testId) {
                $found = true;
                break;
            }
        }
        $manager->deleteEntity($testPerson);
        $this->assertEquals(1,$removedPersons);
        $this->assertFalse($found);
    }


}
