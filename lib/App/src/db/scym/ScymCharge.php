<?php

namespace App\db\scym;

use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Charges
 *
 * @Table(name="charges", indexes={@Index(name="charges_registration_fk", columns={"registrationId"})})
 * @Entity  @HasLifecycleCallbacks
 */
class ScymCharge extends DateStampedEntity implements ICostItem
{
    /**
     * @var integer
     *
     * @Column(name="chargeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $chargeid;

    /**
     * @var ScymRegistration
     *
     * @ManyToOne(targetEntity="ScymRegistration",inversedBy="charges")
     * @JoinColumn(name="registrationId", referencedColumnName="registrationId")
     */
    protected $registration;

    /**
     * Set registration
     *
     * @param ScymRegistration $registration
     * @return ScymCharge
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
     * @var integer
     *
     * @Column(name="feeTypeID", type="integer", nullable=false)
     */
    private $feetypeid = '0';

    /**
     * @var string
     *
     * @Column(name="basis", type="string", length=60, nullable=true)
     */
    private $basis;

    /**
     * @var string
     *
     * @Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @Column(name="notes", type="string", length=100, nullable=true)
     */
    private $notes;


    /**
     * Get chargeid
     *
     * @return integer 
     */
    public function getChargeid()
    {
        return $this->chargeid;
    }

    /**
     * Set feetypeid
     *
     * @param integer $feetypeid
     * @return ScymCharge
     */
    public function setFeetypeid($feetypeid)
    {
        $this->feetypeid = $feetypeid;

        return $this;
    }

    /**
     * Get feetypeid
     *
     * @return integer 
     */
    public function getFeetypeid()
    {
        return $this->feetypeid;
    }

    /**
     * Set basis
     *
     * @param string $basis
     * @return ScymCharge
     */
    public function setBasis($basis)
    {
        $this->basis = $basis;

        return $this;
    }

    /**
     * Get basis
     *
     * @return string 
     */
    public function getBasis()
    {
        return $this->basis;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return ScymCharge
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
     * Set notes
     *
     * @param string $notes
     * @return ScymCharge
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param $amount
     * @param $basis
     * @param $feeTypeId
     * @return ScymCharge
     *
     * Create new charge object
     */
    public static function newCharge($amount,$basis,$feeTypeId)
    {
        $result = new ScymCharge();
        $result->setAmount($amount);
        $result->setBasis($basis);
        $result->setFeetypeid($feeTypeId);
        return $result;
    }
}
