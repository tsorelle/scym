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
    public function testLoadEntity() {
        $this->assertTrue(class_exists('App\db\scym\ScymPerson',true),'Person class not found.');
        // $this->assertTrue(class_exists('\Doctrine\ORM\EntityManager',true),'EntityManager class not found.');

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('configManager','\Tops\sys\TYmlConfigManager');

        $em = TEntityManagers::Get();
        $repository = $em->getRepository('App\db\scym\ScymPerson');
        $person = $repository->findOneBy(array('lastname' => 'SoRelle', 'firstname' => 'Terry'));
        $this->assertNotNull($person,'Person 180 not loaded.');

        $this->assertEquals('Terry',$person->getFirstName());
        $this->assertEquals('SoRelle',$person->getLastName());

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

}
