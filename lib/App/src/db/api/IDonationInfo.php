<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/25/2015
 * Time: 5:53 AM
 */
namespace App\db\api;
use App\db\api\ICostItem;


/**
 * ScymDonation
 *
 * @Table(name="donations")
 * @Entity  @HasLifecycleCallbacks
 */
interface IDonationInfo extends ICostItem
{
    /**
     * Get donationid
     *
     * @return integer
     */
    public function getDonationid();

    /**
     * Get donationtypeid
     *
     * @return integer
     */
    public function getDonationtypeid();

    /**
     * Get registrationid
     *
     * @return integer
     */
    public function getRegistrationid();

}