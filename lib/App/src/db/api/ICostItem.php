<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/19/2015
 * Time: 6:41 AM
 */

namespace App\db\api;


interface ICostItem
{
    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount();

}