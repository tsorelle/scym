<?php
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

}
