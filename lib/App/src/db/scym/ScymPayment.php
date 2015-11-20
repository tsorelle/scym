<?php
namespace App\db\scym;
use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymPayment
 *
 * @Table(name="payments")
 * @Entity  @HasLifecycleCallbacks
 */
class ScymPayment extends DateStampedEntity implements ICostItem
{
    /**
     * @var integer
     *
     * @Column(name="paymentId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $paymentid;

    /**
     * @var \DateTime
     *
     * @Column(name="dateReceived", type="date", nullable=false)
     */
    private $datereceived = '0000-00-00';

    /**
     * @var string
     *
     * @Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var integer
     *
     * @Column(name="paymentType", type="integer", nullable=false)
     */
    private $paymenttype = '0';

    /**
     * @var string
     *
     * @Column(name="checkNumber", type="string", length=10, nullable=true)
     */
    private $checknumber;

    /**
     * @var string
     *
     * @Column(name="payor", type="string", length=80, nullable=true)
     */
    private $payor;

    /**
     * @var integer
     *
     * @Column(name="registrationId", type="integer", nullable=true)
     */
    private $registrationid;


    /**
     * Get paymentid
     *
     * @return integer 
     */
    public function getPaymentId()
    {
        return $this->paymentid;
    }

    /**
     * Set datereceived
     *
     * @param \DateTime $datereceived
     * @return ScymPayment
     */
    public function setDateReceived($datereceived)
    {
        $this->datereceived = $datereceived;

        return $this;
    }

    /**
     * Get datereceived
     *
     * @return \DateTime 
     */
    public function getDateReceived()
    {
        return $this->datereceived;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return ScymPayment
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
     * Set paymenttype
     *
     * @param integer $paymenttype
     * @return ScymPayment
     */
    public function setPaymentType($paymenttype)
    {
        $this->paymenttype = $paymenttype;

        return $this;
    }

    /**
     * Get paymenttype
     *
     * @return integer 
     */
    public function getPaymentType()
    {
        return $this->paymenttype;
    }

    /**
     * Set checknumber
     *
     * @param string $checknumber
     * @return ScymPayment
     */
    public function setCheckNumber($checknumber)
    {
        $this->checknumber = $checknumber;

        return $this;
    }

    /**
     * Get checknumber
     *
     * @return string 
     */
    public function getCheckNumber()
    {
        return $this->checknumber;
    }

    /**
     * Set payor
     *
     * @param string $payor
     * @return ScymPayment
     */
    public function setPayor($payor)
    {
        $this->payor = $payor;

        return $this;
    }

    /**
     * Get payor
     *
     * @return string 
     */
    public function getPayor()
    {
        return $this->payor;
    }

    /**
     * Set registrationid
     *
     * @param integer $registrationid
     * @return ScymPayment
     */
    public function setRegistrationId($registrationid)
    {
        $this->registrationid = $registrationid;

        return $this;
    }

    /**
     * Get registrationid
     *
     * @return integer 
     */
    public function getRegistrationId()
    {
        return $this->registrationid;
    }

}
