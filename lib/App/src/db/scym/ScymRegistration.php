<?php


namespace App\db\scym;

use App\db\api\AttenderDto;
use App\db\api\RegistrationAccount;
use App\db\api\RegistrationDto;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Tops\sys\TKeyValuePair;
use Tops\sys\TListItem;

/**
 * ScymRegistration
 *
 * @Table(name="registrations", uniqueConstraints={@UniqueConstraint(name="registrationcode_idx", columns={"registrationCode"})})
 * @Entity @HasLifecycleCallbacks
 */
class ScymRegistration extends DateStampedEntity implements IRegistration
{
    /**
     * @OneToMany(targetEntity="ScymAttender", mappedBy="registration",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    protected $attenders;

    public function addAttender(ScymAttender $attender) {
        $this->attenders[] = $attender;
        $attender->setRegistration($this);
        return $this;
    }

    public function removeAttender(ScymAttender $attender) {
        $this->attenders->removeElement($attender);
    }

    public function findAttender($id) {
        $attenders = $this->getAttenders();
        foreach ($attenders as $attender) {
            /**
             * @var $attender ScymAttender
             */
            if ($attender->getAttenderId() == $id) {
                return $attender;
            }
        }
        return null;
    }

    public function removeAttenderById($id) {
        $attender = $this->findAttender($id);
        if ($attender) {
            $this->attenders->removeElement($attender);
        }
        return $attender;
    }

    public function removeAccountItems() {
        $removed = array();
        $credits = $this->getCredits()->toArray();
        foreach ($credits as $credit) {
            /**
             * @var $credit ScymCredit
             */
            if ($credit->getCreditid() != 999) { // do not remove manually entered credit
                $this->credits->removeElement($credit);
                array_push($removed,$credit);
            }
        }

        $charges = $this->getCharges()->toArray();
        foreach ($charges as $charge) {
            /**
             * @var $charge ScymCharge
             */
            if ($charge->getFeetypeid() != 999) { // do not remove manually entered charge
                $this->charges->removeElement($charge);
                array_push($removed,$charge);
            }
        }

        return $removed;
    }

    public function updateDonations(array $donationItems) {
        $removed = array();
        $donations = $this->getDonations()->toArray();
        foreach ($donations as $donation) {
            /**
             * @var $charge ScymDonation
             */
            $this->donations->removeElement($donation);
            array_push($removed,$donation);
        }

        foreach ($donationItems as $donationItem) {
            /**
             * @var $donationItem TKeyValuePair
             */
            $newDonation = ScymDonation::createDonation($donationItem->Key,$donationItem->Value);
            $this->addDonation($newDonation);
        }

        return $removed;
    }

    /**
     * @OneToMany(targetEntity="ScymCharge", mappedBy="registration",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    protected $charges;

    public function addcharge(ScymCharge $charge) {
        $this->charges[] = $charge;
        $charge->setRegistration($this);
        return $this;
    }

    public function removecharge(ScymCharge $charge) {
        $this->charges->removeElement($charge);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCharges()  {
        return $this->charges;
    }

    /* ------- Credits -------------------- */
    /**
     * @OneToMany(targetEntity="ScymCredit", mappedBy="registration",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    protected $credits;

    public function addCredit(ScymCredit $credit) {
        $this->credits[] = $credit;
        $credit->setRegistration($this);
        return $this;
    }

    public function removeCredit(ScymCredit $credit) {
        $this->credits->removeElement($credit);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCredits()  {
        return $this->credits;
    }

    /* -------------- Donations ------------- */


    /**
     * @OneToMany(targetEntity="ScymDonation", mappedBy="registration",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    protected $donations;

    public function addDonation(ScymDonation $donation) {
        $this->donations[] = $donation;
        $donation->setRegistration($this);
        return $this;
    }

    public function removeDonation(ScymDonation $donation) {
        $this->donations->removeElement($donation);
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDonations()  {
        return $this->donations;
    }

    public function __construct() {
        $this->attenders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->charges = new \Doctrine\Common\Collections\ArrayCollection();
        $this->credits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->donations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttenders()  {
        return $this->attenders;
    }

    /* ------------ Payments --------------- */

    /**
     * @OneToMany(targetEntity="ScymPayment", mappedBy="registration",fetch="EXTRA_LAZY", cascade={"persist", "remove"})
     */
    protected $payments;

    public function addPayment(ScymPayment $payment) {
        $this->payments[] = $payment;
        $payment->setRegistration($this);
        return $this;
    }

    public function removePayment(ScymPayment $payment) {
        $this->payments->removeElement($payment);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPayments()  {
        return $this->payments;
    }



    /**
     * @var integer
     *
     * @Column(name="registrationId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $registrationId;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var string
     *
     * @Column(name="year", type="string", length=4, nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @Column(name="registrationCode", type="string", length=100, nullable=false)
     */
    private $registrationCode;

    /**
     * @var integer
     *
     * @Column(name="statusId", type="integer", nullable=false)
     */
    private $statusId = '1'; // lookup: registration statustypes

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=100, nullable=true)
     */
    private $username;


    /**
     * @var string
     *
     * @Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @Column(name="address", type="string", length=200, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @Column(name="city", type="string", length=200, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @Column(name="phone", type="string", length=25, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=80, nullable=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @Column(name="receivedDate", type="date", nullable=true)
     */
    private $receivedDate;

    /**
     * @var string
     *
     * @Column(name="amountPaid", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amountPaid;

    /**
     * @var string
     *
     * @Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @Column(name="feesReceivedDate", type="date", nullable=true)
     */
    private $feesReceivedDate;

    /**
     * @var boolean
     *
     * @Column(name="contactRequested", type="boolean", nullable=true)
     */
    private $contactRequested = '0';

    /**
     * @var boolean
     *
     * @Column(name="confirmed", type="boolean", nullable=true)
     */
    private $confirmed = '0';


    /**
     * @var boolean
     *
     * @Column(name="arrivalTime", type="boolean", nullable=true)
     */
    private $arrivalTime;

    /**
     * @var boolean
     *
     * @Column(name="departureTime", type="boolean", nullable=true)
     */
    private $departureTime;

    /**
     * @var string
     *
     * @Column(name="scymNotes", type="text", nullable=true)
     */
    private $scymNotes;

    /**
     * @var \DateTime
     *
     * @Column(name="statusDate", type="date", nullable=true)
     */
    private $statusDate;

    /**
     * @var string
     *
     * @Column(name="YMDonation", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $YMDonation;

    /**
     * @var string
     *
     * @Column(name="simpleMealDonation", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $simpleMealDonation;

    /**
     * @var boolean
     *
     * @Column(name="financialAidRequested", type="boolean", nullable=true)
     */
    private $financialAidRequested = '0';

    /**
     * @var string
     *
     * @Column(name="financialAidContribution", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $financialAidContribution;

    /**
     * @var boolean
     *
     * @Column(name="attended", type="boolean", nullable=true)
     */
    private $attended = '0';

    /**
     * @var string
     *
     * @Column(name="financialAidAmount", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $financialAidAmount = '0.00';


    /**
     * Get registrationId
     *
     * @return integer 
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymRegistration
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set year
     *
     * @param string $year
     * @return ScymRegistration
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set registrationCode
     *
     * @param string $registrationCode
     * @return ScymRegistration
     */
    public function setRegistrationCode($registrationCode)
    {
        $this->registrationCode = $registrationCode;

        return $this;
    }

    /**
     * Get registrationCode
     *
     * @return string 
     */
    public function getRegistrationCode()
    {
        return $this->registrationCode;
    }

    /**
     * Set statusId
     *
     * @param integer $statusId
     * @return ScymRegistration
     */
    public function setStatusId($statusId)
    {
        if ($statusId == 1 || $statusId > $this->statusId) {
            $this->statusDate = new \DateTime();
        }
        if ($this->receivedDate == null && $statusId > 1) {
            $this->receivedDate = new \DateTime();
        }

        $this->statusId = $statusId;
        return $this;
    }

    /**
     * Get statusId
     *
     * @return integer 
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ScymRegistration
     */
    public function setUsername($value)
    {
        $this->username = $value;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ScymRegistration
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return ScymRegistration
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return ScymRegistration
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return ScymRegistration
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ScymRegistration
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set receivedDate
     *
     * @param \DateTime $receivedDate
     * @return ScymRegistration
     */
    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * Get receivedDate
     *
     * @return \DateTime 
     */
    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    /**
     * Set amountPaid
     *
     * @param string $amountPaid
     * @return ScymRegistration
     */
    public function setAmountPaid($amountPaid)
    {
        $this->amountPaid = $amountPaid;

        return $this;
    }

    /**
     * Get amountPaid
     *
     * @return string 
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ScymRegistration
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
     * Set feesReceivedDate
     *
     * @param \DateTime $feesReceivedDate
     * @return ScymRegistration
     */
    public function setFeesReceivedDate($feesReceivedDate)
    {
        $this->feesReceivedDate = $feesReceivedDate;

        return $this;
    }

    /**
     * Get feesReceivedDate
     *
     * @return \DateTime 
     */
    public function getFeesReceivedDate()
    {
        return $this->feesReceivedDate;
    }

    /**
     * Set contactRequested
     *
     * @param boolean $contactRequested
     * @return ScymRegistration
     */
    public function setContactRequested($contactRequested)
    {
        $this->contactRequested = $contactRequested;

        return $this;
    }

    /**
     * Get contactRequested
     *
     * @return boolean 
     */
    public function getContactRequested()
    {
        return $this->contactRequested;
    }

    /**
     * Set contactRequested
     *
     * @param boolean $contactRequested
     * @return ScymRegistration
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * Get confirmed
     *
     * @return boolean
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }


    /**
     * Set arrivalTime
     *
     * @param boolean $arrivalTime
     * @return ScymRegistration
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get arrivalTime
     *
     * @return boolean 
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set departureTime
     *
     * @param boolean $departureTime
     * @return ScymRegistration
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * Get departureTime
     *
     * @return boolean 
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set scymNotes
     *
     * @param string $scymNotes
     * @return ScymRegistration
     */
    public function setScymNotes($scymNotes)
    {
        $this->scymNotes = $scymNotes;

        return $this;
    }

    /**
     * Get scymNotes
     *
     * @return string 
     */
    public function getScymNotes()
    {
        return $this->scymNotes;
    }

    /**
     * Set statusDate
     *
     * @param \DateTime $statusDate
     * @return ScymRegistration
     */
    public function setStatusDate($statusDate)
    {
        $this->statusDate = $statusDate;

        return $this;
    }

    /**
     * Get statusDate
     *
     * @return \DateTime 
     */
    public function getStatusDate()
    {
        return $this->statusDate;
    }

    /**
     * Set YMDonation
     *
     * @param string $YMDonation
     * @return ScymRegistration
     */
    public function setYMDonation($YMDonation)
    {
        $this->YMDonation = $YMDonation;

        return $this;
    }

    /**
     * Get YMDonation
     *
     * @return string 
     */
    public function getYMDonation()
    {
        return $this->YMDonation;
    }

    /**
     * Set simpleMealDonation
     *
     * @param string $simpleMealDonation
     * @return ScymRegistration
     */
    public function setSimpleMealDonation($simpleMealDonation)
    {
        $this->simpleMealDonation = $simpleMealDonation;

        return $this;
    }

    /**
     * Get simpleMealDonation
     *
     * @return string 
     */
    public function getSimpleMealDonation()
    {
        return $this->simpleMealDonation;
    }

    /**
     * Set financialAidRequested
     *
     * @param boolean $financialAidRequested
     * @return ScymRegistration
     */
    public function setFinancialAidRequested($financialAidRequested)
    {
        $this->financialAidRequested = $financialAidRequested;

        return $this;
    }

    /**
     * Get financialAidRequested
     *
     * @return boolean 
     */
    public function getAidAmount()
    {
        return $this->financialAidRequested;
    }

    /**
     * Set financialAidContribution
     *
     * @param string $financialAidContribution
     * @return ScymRegistration
     */
    public function setFinancialAidContribution($financialAidContribution)
    {
        $this->financialAidContribution = $financialAidContribution;

        return $this;
    }

    /**
     * Get financialAidContribution
     *
     * @return string 
     */
    public function getFinancialAidContribution()
    {
        return $this->financialAidContribution;
    }

    /**
     * Set attended
     *
     * @param boolean $attended
     * @return ScymRegistration
     */
    public function setAttended($attended)
    {
        $this->attended = $attended;

        return $this;
    }

    /**
     * Get attended
     *
     * @return boolean 
     */
    public function getAttended()
    {
        return $this->attended;
    }

    /**
     * Set financialAidAmount
     *
     * @param string $financialAidAmount
     * @return ScymRegistration
     */
    public function setFinancialAidAmount($financialAidAmount)
    {
        $this->financialAidAmount = $financialAidAmount;

        return $this;
    }

    /**
     * Get financialAidAmount
     *
     * @return string 
     */
    public function getFinancialAidAmount()
    {
        return $this->financialAidAmount;
    }

    /**
     * @return \stdClass
     */
    public function getDataTransferObject() {
        $result = new \stdClass();
        $result->registrationId           = $this->registrationId;
        $result->registrationCode         = $this->registrationCode;
        $result->name                     = $this->name;
        $result->address1                 = $this->address;
        $result->city                     = $this->city  ;
        $result->phone                    = $this->phone ;
        $result->email                    = $this->email ;
        $result->receivedDate             = $this->formatDtoDate($this->receivedDate);
        $result->amountPaid               = $this->amountPaid;
        $result->notes                    = $this->notes ;
        $result->feesReceivedDate         = $this->formatDtoDate($this->feesReceivedDate);
        $result->contactRequested         = $this->contactRequested;
        $result->confirmed                = $this->confirmed;
        $result->arrivalTime              = $this->arrivalTime;
        $result->departureTime            = $this->departureTime;
        $result->scymNotes                = $this->scymNotes;
        $result->active                   = $this->active;
        $result->YMDonation               = $this->YMDonation;
        $result->simpleMealDonation       = $this->simpleMealDonation;
        $result->financialAidRequested    = $this->financialAidRequested;
        $result->financialAidContribution = $this->financialAidContribution;
        $result->attended                 = $this->attended;
        $result->financialAidAmount       = $this->financialAidAmount;
        $result->statusDate               = $this->formatDtoDate($this->statusDate);
        $result->statusId                 = $this->statusId;
        $result->priorStatus              = $this->statusId;

        return $result;
    }

    public static function createNewRegistration(RegistrationDto $dto) {
        $result = new ScymRegistration();

        $result->updateFromDataTransferObject($dto);
        return $result;
    }

    public function updateFromDataTransferObject(RegistrationDto $dto, $updateAdminFields = false)
    {
        if ($dto->getRegistrationId() < 1) {
            $this->registrationCode = $dto->getRegistrationCode();
        }
        $this->setStatusId($dto->getStatusId());

        $this->name                     = $dto->getName();
        $this->address                  = $dto->getAddress();
        $this->city                     = $dto->getCity();
        $this->phone                    = $dto->getPhone();
        $this->email                    = $dto->getEmail();
        $this->notes                    = $dto->getNotes();
        $this->contactRequested         = $dto->getContactRequested();
        $this->financialAidAmount       = $dto->getFinancialAidAmount();

        if ($updateAdminFields) {
            $this->scymNotes = $dto->getScymNotes();
            $this->feesReceivedDate = $dto->getFeesReceivedDate();
            // $this->financialAidAmount = $dto->getFinancialAidAmount();
            // $this->amountPaid               = $dto->getAmountPaid();
            // $this->arrivalTime              = $dto->getArrivalTime();
            // $this->departureTime            = $dto->getDepartureTime();
        }

        return true;
    }

    public function addAccountItems(RegistrationAccount $account) {
        $charges = $account->getCharges();
        foreach ($charges as $charge) {
            $this->addcharge($charge);
        }
        $credits = $account->getCredits();
        foreach ($credits as $credit) {
            $this->addCredit($credit);
        }
    }

    /**
     * @return TListItem[]
     */
    public function getAttenderList()
    {
        $result = array();
        $attenders = $this->getAttenders()->toArray();
        foreach ($attenders as $attender) {
            /**
             * @var $attender ScymAttender
             */
            TListItem::AddToArray($result,$attender->getFullName(),$attender->getAttenderId());
        }
        return $result;
    }

    /**
     * @param array $attenderUpdates  IAttender[]
     */
    public function updateAttenders(array $attenderUpdates) {
        foreach ($attenderUpdates as $dto) {
            /**
             * @var $dto AttenderDto
             */
            $id = $dto->getAttenderId();
            if ($id < 1) {
                /**
                 * @var $dto AttenderDto
                 */
                $attender = ScymAttender::CreateAttender($dto);
                $this->addAttender($attender);
            }
            else {
                $attender = $this->findAttender($id);
                $attender->updateFromDataTransferObject($dto);
                $mealtimes = $dto->getMeals();
                if ($mealtimes != null) {
                    $attender->updateMeals($mealtimes);
                }
            }
        }
    }

    public function addAttenders(array $newAttenders) {
        foreach($newAttenders as $dto) {
            /**
             * @var $dto AttenderDto
             */
            $attender = ScymAttender::CreateAttender($dto);
            $this->addAttender($attender);
        }

    }

}
