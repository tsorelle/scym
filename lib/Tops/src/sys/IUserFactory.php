<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 4/24/2015
 * Time: 4:15 AM
 */

namespace Tops\sys;


use Tops\sys\IUser;

interface IUserFactory {
    /**
     * @return IUser
     */
    public function createUser();
}