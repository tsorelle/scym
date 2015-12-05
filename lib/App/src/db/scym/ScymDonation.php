<?php

namespace App\db\scym;

use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use App\db\api\IDonationInfo;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymDonation
 *
 * @Table(name="donations", indexes={@Index(name="donations_registration_fk", columns={"registrationId"})})
 * @Entity  @HasLifecycleCallbacks
 */
class ScymDonation extends DateStampedEntity implements IDonationInfo
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
     * @var ScymRegistration
     *
     * @ManyToOne(targetEntity="ScymRegistration",inversedBy="donations")
     * @JoinColumn(name="registrationId", referencedColumnName="registrationId")
     */
    protected $registration;

    /**
     * Set registration
     *
     * @param ScymRegistration $registration
     * @return ScymDonation
     */
    public function setRegistration(ScymRegistration $registration = null)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Get registration
     *
     * @return ScymRegistration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    public function getRegistrationId()
    {
        return $this->registration ? $this->registration->getRegistrationid() : null;
    }


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

    public static function createDonation($donationTypeId,$amount,$note = null)
    {
        $result = new ScymDonation();
        $result->setDonationtypeid($donationTypeId);
        $result->setAmount($amount);
        if (!empty($note)) {
            $result->setNote($note);
        }
        return $result;
    }
}
