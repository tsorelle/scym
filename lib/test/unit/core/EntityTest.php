<?php
/**
 * Created by PhpStorm.
 * User: Terry SoRelle
 * Date: 1/17/2015
 * Time: 9:26 AM
 */

use Tops\db\TEntityManagers;
// use App\db\ScymPerson;

class EntityTest extends PHPUnit_Framework_TestCase {

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @return null|\App\db\scym\ScymPerson
     */
    private function findTestPerson(\Doctrine\ORM\EntityManager $em) {
        $repository = $em->getRepository('App\db\scym\ScymPerson');
        return $repository->findOneBy(array('lastname' => 'SoRelle', 'firstname' => 'Terry'));
    }

    public function testLoadAddress() {
        $this->assertTrue(class_exists('App\db\scym\ScymAddress',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymAddress');

        $address = $repository->find(117);

        $this->assertNotNull($address,'Address not loaded.');

        $this->assertNotNull($address);
        $persons = $address->getPersons();
        foreach ($persons as $p) {
            $name = $p->getFirstName();
        }

    }

    public function testLoadEntity() {
        $this->assertTrue(class_exists('App\db\scym\ScymPerson',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();

        $repository = $em->getRepository('App\db\scym\ScymPerson');

        $person = $repository->find(180);
        //findOneBy(array('lastname' => 'SoRelle', 'firstname' => 'Terry'));
        // $person = $this->findTestPerson($em);


        $this->assertNotNull($person,'Person 180 not loaded.');

        $this->assertEquals('Terry',$person->getFirstName());
        $this->assertEquals('SoRelle',$person->getLastName());
        $id = $person->getAddressId();
        $address = $person->getAddress();

        $this->assertNotNull($address);
        $persons = $address->getPersons();
        foreach ($persons as $p) {
            print $p->getFirstName();
        }

    }

    public function testDateStamp() {
        $this->assertTrue(class_exists('App\db\scym\ScymPerson',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymPerson');

        /**
         * @var \App\db\scym\ScymPerson
         */
        $person = $repository->findOneBy(array('lastname' => 'SoRelle', 'firstname' => 'Terry'));
        // $person = $repository->find(180);

        $this->assertNotNull($person,'Person 180 not loaded.');

        $updateDate = $person->getDateUpdated();
        $this->assertNotNull($updateDate);
        $person->setSortKey("sorelle,terry: ".time());
        $em->flush();
        $person = $repository->findOneBy(array('lastname' => 'SoRelle', 'firstname' => 'Terry'));
        $actual = $person->getDateUpdated();
        $this->assertNotEquals($updateDate,$actual);

    }



    public function testLoadMailboxEntity() {
        $this->assertTrue(class_exists('App\db\scym\ScymMailbox',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymMailbox');
        $mailbox = $repository->findOneBy(array('box' => 'clerk'));
        $this->assertNotNull($mailbox,'Mailbox not loaded.');
        $this->assertEquals('SCYM Clerk', $mailbox->getName());

    }

    public function testLoadMeetingEntity() {
        $this->assertTrue(class_exists('App\db\scym\ScymMailbox',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymMeeting');
        $meeting = $repository->findOneBy(array('affiliationcode' => 'AU TX'));
        $this->assertNotNull($meeting,'Meeting not loaded.');
        $actual = $meeting->getMeetingName();
        $expected = 'Friends Meeting of Austin';
        $this->assertEquals($expected,$actual);

        $quarterly = $meeting->getQuarterlyMeeting();
        $this->assertNotNull($quarterly);
        $expected = 'Cielo Grande';
        $actual = $quarterly->getQuarterlyMeetingName();
        $this->assertEquals($expected,$actual);

    }

    public function testLoadQuarterlyMeetingEntity() {
        $this->assertTrue(class_exists('App\db\scym\ScymQuarterlyMeeting',true),'Quarterly meeting class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymQuarterlyMeeting');
        $meeting = $repository->findOneBy(array('quarterlymeetingname' => 'Cielo Grande'));
        $this->assertNotNull($meeting,'Quarterly Meeting not loaded.');
        $actual = $meeting->getQuarterlyMeetingId();
        $expected = 1;
        $this->assertEquals($expected,$actual);

        $meetings = $meeting->getMeetings();
        $this->assertNotEmpty($meetings);
        $found = false;
        foreach($meetings as $meeting) {
            if ($meeting->getAffiliationcode() == 'AU TX') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);


    }
}
