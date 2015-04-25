<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/24/2015
 * Time: 4:28 AM
 */

namespace Tops\test\unit\core;


use Tops\sys\IUser;
use Tops\sys\TObjectContainer;
use Tops\sys\TUser;

class UserFactoryTest extends \PHPUnit_Framework_TestCase {

    /**
     *  Prove that multiple calls to TObjectContainer::Get
     *  return the same instance.
     */
    public function testSingleton() {
        \Tops\sys\TObjectContainer::Clear();
        TObjectContainer::Register('tops.user','Tops\test\TTestUser');

        /** @var IUser $first */
        $first = TObjectContainer::Get('tops.user');
        $first->setProfileValue('test','one');

        /** @var IUser $second */
        $second = TObjectContainer::Get('tops.user');
        $test = $second->getProfileValue('test');

        $this->assertNotEquals($test,'test');

    }

    /**
     *  Prove that multiple calls to TUser::getByUserName (using a IUserFactory object)
     *  return distinct instances.
     */
    public function testMultipleInstance() {
        \Tops\sys\TObjectContainer::Clear();
        TObjectContainer::Register('tops.userfactory','Tops\test\TTestUserFactory');
        $firstInstance = TUser::getByUserName('one');
        $firstInstance->setProfileValue("test","one");
        $secondInstance = TUser::getByUserName('two');
        $actual = $secondInstance->getProfileValue('test');
        $this->assertNotEquals($actual,"test");
    }
}
