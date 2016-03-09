<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/25/2015
 * Time: 5:57 AM
 */

namespace App\db\api;

namespace App\db\api;

use App\db\api\IDonationInfo;

/**
 * Class DonationFacade
 * @package App\db\api
 *
 * Wraps an array of IKeyValuePair to present Donation object
 *
 */
class DonationFacade implements IDonationInfo
{
    /**
     * @var \stdClass[]
     *
     * Element is key/value pair
     */
    private $donation;

    public function __construct(\stdClass $donation)
    {
        $this->donation = $donation;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return (isset($this->donation->Value)) ? $this->donation->Value : 0.00;
    }

    /**
     * Get donationid
     *
     * @return integer
     */
    public function getDonationid()
    {
        return 0;
    }

    /**
     * Get donationtypeid
     *
     * @return integer
     */
    public function getDonationtypeid()
    {
        return (isset($this->donation->Key)) ? $this->donation->Key : 0;
    }

    /**
     * Get registrationid
     *
     * @return integer
     */
    public function getRegistrationid()
    {
        return 0;
    }
}