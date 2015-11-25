<?php

namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScymDonationType
 *
 * @Table(name="donationtypes")
 * @Entity
 */
class ScymDonationType
{
    /**
     * @var integer
     *
     * @Column(name="donationTypeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $donationtypeid = '0';

    /**
     * @var string
     *
     * @Column(name="donationTypeCode", type="string", length=10, nullable=true)
     */
    private $donationtypecode;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=80, nullable=true)
     */
    private $description;


    /**
     * Get donationtypeid
     *
     * @return integer 
     */
    public function getDonationtypeid()
    {
        return $this->donationtypeid;
    }

    /**
     * Set donationtypecode
     *
     * @param string $donationtypecode
     * @return ScymDonationType
     */
    public function setDonationtypecode($donationtypecode)
    {
        $this->donationtypecode = $donationtypecode;

        return $this;
    }

    /**
     * Get donationtypecode
     *
     * @return string 
     */
    public function getDonationtypecode()
    {
        return $this->donationtypecode;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymDonationType
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @var string
     *
     * @Column(name="fundName", type="string", length=80, nullable=true)
     */
    private $fundname;

    /**
     * @var boolean
     *
     * @Column(name="registrationForm", type="boolean", nullable=true)
     */
    private $registrationform = '1';


    /**
     * Set fundname
     *
     * @param string $fundname
     * @return ScymDonationType
     */
    public function setFundname($fundname)
    {
        $this->fundname = $fundname;

        return $this;
    }

    /**
     * Get fundname
     *
     * @return string 
     */
    public function getFundname()
    {
        return $this->fundname;
    }

    /**
     * Set registrationform
     *
     * @param boolean $registrationform
     * @return ScymDonationType
     */
    public function setRegistrationform($registrationform)
    {
        $this->registrationform = $registrationform;

        return $this;
    }

    /**
     * Get registrationform
     *
     * @return boolean 
     */
    public function getRegistrationform()
    {
        return $this->registrationform;
    }

    public function toLookupItem() {
        $result = new \stdClass();
        $result->Key = $this->donationtypeid;
        $result->Text = $this->fundname;
        $result->Description = $this->description;
        return $result;
    }
}
