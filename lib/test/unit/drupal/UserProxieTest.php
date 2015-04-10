<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/10/2015
 * Time: 5:31 AM
 */



class UserProxieTest extends PHPUnit_Framework_TestCase {
    protected function setUp()
    {
        $fakeUser = new \stdClass();
        $fakeUser->uid = 2;
        $fakeUser->name = 'terry';
        $GLOBALS['user'] = $fakeUser;

        \Tops\sys\TObjectContainer::Clear();
        \Tops\sys\TObjectContainer::Register('current_user','\Tops\drupal\TDrupalAccountProxy');

    }

    public function testDrupalCurrentUser()
    {
        $actual = Drupal::CurrentUser();
        $this->assertNotNull($actual);
        $this->assertEquals('terry',$actual->getUsername());
        $this->assertEquals(2,$actual->id());
    }




}
