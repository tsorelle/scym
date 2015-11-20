<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 9:50 AM
 */
namespace App\db\api;


/**
 * Payments
 *
 */
interface IPaymentInfo
{
    /**
     * Get paymentid
     *
     * @return integer
     */
    public function getPaymentid();

    /**
     * Get datereceived
     *
     * @return \DateTime
     */
    public function getDatereceived();

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount();

    /**
     * Get checknumber
     *
     * @return string
     */
    public function getChecknumber();

    /**
     * Get payor
     *
     * @return string
     */
    public function getPayor();
}