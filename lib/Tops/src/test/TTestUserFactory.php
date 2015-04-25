<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/24/2015
 * Time: 4:26 AM
 */

namespace Tops\test;


use Tops\sys\IUser;
use Tops\test\TTestUser;
use Tops\sys\IUserFactory;

class TTestUserFactory implements IUserFactory {

    /**
     * @return IUser
     */
    public function createUser()
    {
        return new TTestUser();
    }
}