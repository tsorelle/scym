<?php

namespace App\db\scym;


use App\db\api\ICostItem;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 *
 * @Table(name="credits", indexes={@Index(name="credits_registration_fk", columns={"registrationId"})})
 * @Entity @HasLifecycleCallbacks
 */
class ScymCredit extends DateStampedEntity implements ICostItem
{
    /**
     * @var integer
     *
     * @Column(name="creditId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $creditid;

    /**
     * @var ScymRegistration
     *
     * @ManyToOne(targetEntity="ScymRegistration",inversedBy="credits")
     * @JoinColumn(name="registrationId", referencedColumnName="registrationId")
     */
    protected $registration;

    /**
     * Set registration
     *
     * @param ScymRegistration $registration
     * @return ScymCredit
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
     * @Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @Column(name="notes", type="string", length=200, nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=50, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @Column(name="creditTypeId", type="integer", nullable=true)
     */
    private $credittypeid;


    /**
     * Get creditid
     *
     * @return integer 
     */
    public function getCreditid()
    {
        return $this->creditid;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return ScymCredit
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
     * @return ScymCredit
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
     * Set description
     *
     * @param string $description
     * @return ScymCredit
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
     * Set credittypeid
     *
     * @param integer $credittypeid
     * @return ScymCredit
     */
    public function setCredittypeid($credittypeid)
    {
        $this->credittypeid = $credittypeid;

        return $this;
    }

    /**
     * Get credittypeid
     *
     * @return integer 
     */
    public function getCredittypeid()
    {
        return $this->credittypeid;
    }

    /**
     * @param $amount
     * @param $note
     * @param $creditTypeId
     * @return ScymCredit
     *
     * Create new credit instance.
     */
    public static function newCredit($amount,$description,$creditTypeId,$notes=null) {
        $result = new ScymCredit();
        $result->setAmount($amount);
        $result->setDescription($description);
        $result->setCredittypeid($creditTypeId);
        if (!empty($notes)) {
            $result->setNotes($notes);
        }
        return $result;
    }
    
    public static function CreateCredit($dto) {
        $amount = isset($dto->amount) ? $dto->amount : null;
        if(empty($amount)) {
            return null;
        }
        $creditTypeId = isset($dto->creditTypeId) ? $dto->creditTypeId : null;
        if (empty($creditTypeId)) {
            return null;
        }
        $description = isset($dto->description) ? $dto->description : null;
        if (empty($description)) {
            return null;
        }
        $notes = isset($dto->notes) ? $dto->notes : null;
        return ScymCredit::newCredit($amount,$description,$creditTypeId,$notes);
    }
}
