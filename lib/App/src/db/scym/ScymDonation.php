<?php

namespace App\db\scym;

use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymDonation
 *
 * @Table(name="donations")
 * @Entity  @HasLifecycleCallbacks
 */
class ScymDonation extends DateStampedEntity implements ICostItem
{
    /**
     * @var integer
     *
     * @Column(name="donationId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $donationid;

    /**
     * @var integer
     *
     * @Column(name="donationTypeId", type="integer", nullable=true)
     */
    private $donationtypeid;

    /**
     * @var integer
     *
     * @Column(name="registrationId", type="integer", nullable=false)
     */
    private $registrationid = '0';

    /**
     * @var string
     *
     * @Column(name="amount", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @Column(name="note", type="string", length=100, nullable=true)
     */
    private $note;


    /**
     * Get donationid
     *
     * @return integer 
     */
    public function getDonationid()
    {
        return $this->donationid;
    }

    /**
     * Set donationtypeid
     *
     * @param integer $donationtypeid
     * @return ScymDonation
     */
    public function setDonationtypeid($donationtypeid)
    {
        $this->donationtypeid = $donationtypeid;

        return $this;
    }

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
     * Set registrationid
     *
     * @param integer $registrationid
     * @return ScymDonation
     */
    public function setRegistrationid($registrationid)
    {
        $this->registrationid = $registrationid;

        return $this;
    }

    /**
     * Get registrationid
     *
     * @return integer 
     */
    public function getRegistrationid()
    {
        return $this->registrationid;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return ScymDonation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ScymDonation
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }
}
