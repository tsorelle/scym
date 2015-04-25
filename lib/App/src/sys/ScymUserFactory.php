<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/24/2015
 * Time: 5:14 AM
 */

namespace App\sys;


use Tops\sys\IUser;
use Tops\sys\IUserFactory;

class ScymUserFactory implements IUserFactory{

    /**
     * @return IUser
     */
    public function createUser()
    {
        return new ScymUser();
    }
}