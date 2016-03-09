<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/18/2015
 * Time: 11:39 AM
 */
namespace App\db\api;


/**
 *
 *
 * @Table(name="credits")
 * @Entity @HasLifecycleCallbacks
 */
interface ICreditInfo
{
    /**
     * Get creditid
     *
     * @return integer
     */
    public function getCreditid();

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount();

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes();

    /**
     * Get credittypeid
     *
     * @return integer
     */
    public function getCredittypeid();
}