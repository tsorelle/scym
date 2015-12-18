<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 12/3/2015
 * Time: 10:19 AM
 */

namespace App\db\api;

use Tops\sys\TDateTime;

class RegistrationDto implements IRegistration
{
    /**
     * @var \stdClass
     */
    private $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    private function getDateValue($value,$default = null) {
        if (empty($value)) {
            return $default;
        }
        try {
            $result = new \DateTime($value);
        }
        catch (\Exception $ex) {
            $result = null;
        }
        if (TDateTime::isEmpty($result)) {
            return $default;
        }
        return $result;
    }



    /**
     * Get registrationId
     *
     * @return integer
     */
    public function getRegistrationId()
    {
        return isset($this->data->registrationId) ? $this->data->registrationId  : null;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return isset($this->data->year) ? $this->data->year : null;
    }

    /**
     * Get registrationCode
     *
     * @return string
     */
    public function getRegistrationCode()
    {
        return isset($this->data->registrationCode) ? $this->data->registrationCode: null;
    }

    /**
     * Get statusId
     *
     * @return integer
     */
    public function getStatusId()
    {
        return isset($this->data->statusId) ? $this->data->statusId : 1;
    }

    public function setStatusId($value) {
        $this->data->statusId = $value;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getUsername()
    {
        return isset($this->data->userName) ? $this->data->userName : null;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return isset($this->data->name) ? $this->data->name : null;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return isset($this->data->address) ? $this->data->address : null;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return isset($this->data->city) ? $this->data->city : null;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return isset($this->data->phone) ? $this->data->phone : null;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return isset($this->data->email) ? $this->data->email : null;
    }

    /**
     * Get receivedDate
     *
     * @return \DateTime
     */
    public function getReceivedDate()
    {
        $result = (isset($this->data->receivedDate)) ? $this->getDateValue($this->data->receivedDate) : null;
        if (TDateTime::isEmpty($result)) {
            return null; // new \DateTime();
        }
        return $result;
    }


    /**
     * Get statusdDate
     *
     * @return \DateTime
     */
    public function getStatusDate()
    {
        $result = (isset($this->data->statusDate)) ? $this->getDateValue($this->data->statusDate) : null;
        if (TDateTime::isEmpty($result)) {
            return new \DateTime();
        }
        return $result;
    }


    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return isset($this->data->notes) ? $this->data->notes : null;
    }

    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed()
    {
        return isset($this->data->confirmed) ? !empty($this->data->confirmed) : false;
    }

    /**
     * Get scymNotes
     *
     * @return string
     */
    public function getScymNotes()
    {
        return isset($this->data->scymNotes) ? $this->data->scymNotes : null;
    }

    /**
     * Get financialAidContribution
     *
     * @return string
    public function getFinancialAidContribution()
    {
    }
     */

    /**
     * Get financialAidAmount
     *
     * @return string
     */
    public function getFinancialAidAmount()
    {
        return isset($this->data->financialAidAmount) ? $this->data->financialAidAmount : 0.00;
    }

    /**
     * @return \stdClass
     */
    public function getData() {
        return $this->data;
    }
}