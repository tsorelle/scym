<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 11:29 AM
 */
namespace App\db\api;


interface IChargeInfo
{
    /**
     * Get chargeid
     *
     * @return integer
     */
    public function getChargeid();

    /**
     * Get feetypeid
     *
     * @return integer
     */
    public function getFeetypeid();

    /**
     * Get basis
     *
     * @return string
     */
    public function getBasis();

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount();
}