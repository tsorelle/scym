<?php
namespace App\db\scym;
use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymPayment
 *
 * @Table(name="payments", indexes={@Index(name="payments_registration_fk", columns={"registrationId"})})
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
     * @var string
     *
     * @Column(name="notes", type="string", length=200, nullable=true)
     */
    private $notes;


    /**
     * @var ScymRegistration
     *
     * @ManyToOne(targetEntity="ScymRegistration",inversedBy="payments")
     * @JoinColumn(name="registrationId", referencedColumnName="registrationId")
     */
    protected $registration;

    /**
     * Set registration
     *
     * @param ScymRegistration $registration
     * @return ScymPayment
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
     * Set notes
     *
     * @param string $notes
     * @return ScymPayment
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
     * @param $paymentDto
     *     amount: number
     *     payor: string;
     *    checkNumber: string;
     */
    public static function CreatePayment($paymentDto,$paymentType='check')
    {
        $payment = new ScymPayment();
        $payment->setAmount($paymentDto->amount);
        $payment->setCheckNumber($paymentDto->checkNumber);
        $payment->setPayor($paymentDto->payor);
        switch($paymentType) {
            case 'cash' : $paymentType = 1;
                break;
            case 'check' : $paymentType = 2;
                break;
            default : $paymentType = 0;
        }
        $payment->setPaymentType($paymentType);
        $payment->setDateReceived(new DateTime());
        if (isset($paymentDto->notes)){
            $payment->setNotes($paymentDto->notes);
        }

        return $payment;
    }

    /**
     * @param $paymentDto
     *     amount: number
     *    type: 'cash' or 'check;
     *     payor: string;
     *    checkNumber: string;
     */
    public static function validatePayment($paymentDto)
    {
        $result = new \stdClass();
        $result->errorMessage = '';
        $result->amount = isset($paymentDto->amount) ? $paymentDto->amount : null;
        if (isset($paymentDto->notes)) {
            $result->notes = $paymentDto->notes;
        }
        if (empty($result->amount)) {
            $result->errorMessage = 'No amount';
            return $result;
        }
        $result->payor = isset($paymentDto->payor) ? $paymentDto->payor : null;
        if (empty($result->payor)) {
            $result->errorMessage = 'No payor';
            return $result;
        }

        $type = isset($paymentDto->type) ? $paymentDto->type : null;
        $checkNumber = isset($paymentDto->checkNumber) ? $paymentDto->checkNumber : '';

        if (empty($type) && !empty($checkNumber)) {
            $type = 'check';
        }

        if (empty($type)) {
            $result->errorMessage = 'No payment type';
            return $result;
        }

        if ($type == 'check') {
            if (empty($checkNumber)) {
                $result->errorMesssage = 'No check number';
            }
            $result->checkNumber = $checkNumber;
        }
        else {
            $result->checkNumber = 'cash';
        }

        $result->notes = isset($paymentDto->notes) ? $paymentDto->notes : '';

        return $result;
    }
}
