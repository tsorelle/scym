<?php


namespace App\db\scym;

use App\db\api\AttenderDto;
use App\db\api\IAttenderCostInfo;
use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;
use App\db\scym\ScymMeal;
/**
 * ScymAttender
 *
 * @Table(name="attenders", indexes={@Index(name="attenders_registration_fk", columns={"registrationId"})})
 * @Entity @HasLifecycleCallbacks
 */
class ScymAttender extends DateStampedEntity implements IAttenderCostInfo
{
    public function __construct()
    {
        $this->meals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @OneToMany(targetEntity="ScymMeal", mappedBy="attender",fetch="EAGER", cascade={"persist", "remove"})
     */
    protected $meals;

    public function addMeal(ScymMeal $meal) {
        $this->meals[] = $meal;
        $meal->setAttender($this);
        return $this;
    }

    public function removeMeal(ScymMeal $meal) {
        $this->meals->removeElement($meal);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMeals()  {
        return $this->meals;
    }


    /**
     * @var integer
     *
     * @Column(name="attenderID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $attenderId;

    /**
     * @var string
     *
     * @Column(name="firstName", type="string", length=50, nullable=false)
     */
    private $firstName = '';

    /**
     * @var string
     *
     * @Column(name="lastName", type="string", length=50, nullable=false)
     */
    private $lastName = '';

    /**
     * @var string
     *
     * @Column(name="middleName", type="string", length=50, nullable=true)
     */
    private $middleName;

    /**
     * @var \DateTime
     *
     * @Column(name="dateOfBirth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @Column(name="affiliationCode", type="string", length=20, nullable=true)
     */
    private $affiliationCode;

    /**
     * @var string
     *
     * @Column(name="otherAffiliation", type="string", length=30, nullable=true)
     */
    private $otherAffiliation;

    /**
     * @var boolean
     *
     * @Column(name="firstTimer", type="boolean", nullable=true)
     */
    private $firstTimer = '0';

    /**
     * @var boolean
     *
     * @Column(name="teacher", type="boolean", nullable=true)
     */
    private $teacher = '0';

    /**
     * @var boolean
     *
     * @Column(name="financialAidRequested", type="boolean", nullable=true)
     */
    private $financialAidRequested = '0';

    /**
     * @var boolean
     *
     * @Column(name="guest", type="boolean", nullable=true)
     */
    private $guest = '0';

    /**
     * @var string
     *
     * @Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @Column(name="linens", type="boolean", nullable=true)
     */
    private $linens = '0';

    /**
     * @var integer
     *
     * @Column(name="specialNeedsTypeId", type="integer", nullable=true)
     */
    private $specialNeedsTypeId; // lookup: special needs

    /**
     * @var int
     *
     * @Column(name="arrivalTime", type="integer", nullable=true)
     */
    private $arrivalTime = '41';

    /**
     * @var int
     *
     * @Column(name="departureTime", type="integer", nullable=true)
     */
    private $departureTime = '72';

    /**
     * @var integer
     *
     * @Column(name="housingTypeId", type="integer", nullable=true)
     */
    private $housingTypeId; // lookup: housingTypes

    /**
     * @var boolean
     *
     * @Column(name="vegetarian", type="boolean", nullable=true)
     */
    private $vegetarian = '0';

    /**
     * @var boolean
     *
     * @Column(name="attended", type="boolean", nullable=true)
     */
    private $attended = '0';

    /**
     * @var int
     *
     * @Column(name="generationId", type="integer", nullable=true)
     */
    private $generationId = '1'; // lookup: generations

    /**
     * @var string
     *
     * @Column(name="gradeLevel", type="string", length=2, nullable=true)
     */
    private $gradeLevel; // 'PS','K', 1 .. 13

    /**
     * @var integer
     *
     * @Column(name="ageGroupId", type="integer", nullable=true)
     */
    private $ageGroupId; // lookup agegroups

    /**
     * @var integer
     *
     * @Column(name="creditTypeId", type="integer", nullable=true)
     */
    private $creditTypeId = '0'; // formerly: feeCredit, lookup: creditTypes

    /**
     * @var boolean
     *
     * @Column(name="singleOccupant", type="boolean", nullable=true)
     */
    private $singleOccupant = '0';

    /**
     * @var boolean
     *
     * @Column(name="glutenFree", type="boolean", nullable=true)
     */
    private $glutenFree = '0';

    /**
     * @var ScymRegistration
     *
     * @ManyToOne(targetEntity="ScymRegistration",inversedBy="attenders")
     * @JoinColumn(name="registrationId", referencedColumnName="registrationId")
     */
    protected $registration;

    public function getRegistrationId()
    {
        return $this->registration ? $this->registration->getRegistrationid() : null;
    }

    /**
     * Get attenderid
     *
     * @return integer
     */
    public function getAttenderId()
    {
        return $this->attenderId;
    }

    /**
     * Set firstname
     *
     * @param string $firstName
     * @return ScymAttender
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstName;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return ScymAttender
     */
    public function setLastName($lastname)
    {
        $this->lastName = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     * @return ScymAttender
     */
    public function setMiddleName($middlename)
    {
        $this->middleName = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middleName;
    }

    /**
     * Set dateofbirth
     *
     * @param \DateTime $dateofbirth
     * @return ScymAttender
     */
    public function setDateofbirth($dateofbirth)
    {
        $this->dateOfBirth = $dateofbirth;

        return $this;
    }

    /**
     * Get dateofbirth
     *
     * @return \DateTime
     */
    public function getDateofbirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set affiliationcode
     *
     * @param string $affiliationcode
     * @return ScymAttender
     */
    public function setAffiliationCode($affiliationcode)
    {
        $this->affiliationCode = $affiliationcode;

        return $this;
    }

    /**
     * Get affiliationcode
     *
     * @return string
     */
    public function getAffiliationCode()
    {
        return $this->affiliationCode;
    }

    /**
     * Set otheraffiliation
     *
     * @param string $otheraffiliation
     * @return ScymAttender
     */
    public function setOtherAffiliation($otheraffiliation)
    {
        $this->otherAffiliation = $otheraffiliation;

        return $this;
    }

    /**
     * Get otheraffiliation
     *
     * @return string
     */
    public function getOtherAffiliation()
    {
        return $this->otherAffiliation;
    }

    /**
     * Set firsttimer
     *
     * @param boolean $firsttimer
     * @return ScymAttender
     */
    public function setFirstTimer($firsttimer)
    {
        $this->firstTimer = $firsttimer;

        return $this;
    }

    /**
     * Get firsttimer
     *
     * @return boolean
     */
    public function getFirstTimer()
    {
        return $this->firstTimer;
    }

    /**
     * Set teacher
     *
     * @param boolean $teacher
     * @return ScymAttender
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return boolean
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set financialaidrequested
     *
     * @param boolean $financialaidrequested
     * @return ScymAttender
     */
    public function setFinancialAidRequested($financialaidrequested)
    {
        $this->financialAidRequested = $financialaidrequested;

        return $this;
    }

    /**
     * Get financialaidrequested
     *
     * @return boolean
     */
    public function getFinancialAidRequested()
    {
        return $this->financialAidRequested;
    }

    /**
     * Set guest
     *
     * @param boolean $guest
     * @return ScymAttender
     */
    public function setGuest($guest)
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * Get guest
     *
     * @return boolean
     */
    public function getGuest()
    {
        return $this->guest;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ScymAttender
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
     * Set linens
     *
     * @param boolean $linens
     * @return ScymAttender
     */
    public function setLinens($linens)
    {
        $this->linens = $linens;

        return $this;
    }

    /**
     * Get linens
     *
     * @return boolean
     */
    public function getLinens()
    {
        return $this->linens;
    }

    /**
     * Set specialneeds
     *
     * @param integer $specialneeds
     * @return ScymAttender
     */
    public function setSpecialNeedsTypeId($specialneeds)
    {
        $this->specialNeedsTypeId = $specialneeds;

        return $this;
    }

    /**
     * Get specialneeds
     *
     * @return integer
     */
    public function getSpecialNeedsTypeId()
    {
        return $this->specialNeedsTypeId;
    }

    /**
     * Set arrivaltime
     *
     * @param int $arrivaltime
     * @return ScymAttender
     */
    public function setArrivalTime($arrivaltime)
    {
        $this->arrivalTime = $arrivaltime;

        return $this;
    }

    /**
     * Get arrivaltime
     *
     * @return int
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set departuretime
     *
     * @param int $departuretime
     * @return ScymAttender
     */
    public function setDepartureTime($departuretime)
    {
        $this->departureTime = $departuretime;

        return $this;
    }

    /**
     * Get departuretime
     *
     * @return int
     */
    public function getDeparturetime()
    {
        return $this->departureTime;
    }

    /**
     * Set housingTypeId
     *
     * @param integer $housingTypeId
     * @return ScymAttender
     */
    public function setHousingTypeId($housingTypeId)
    {
        $this->housingTypeId = $housingTypeId;

        return $this;
    }

    /**
     * Get housingTypeId
     *
     * @return integer
     */
    public function getHousingTypeId()
    {
        return $this->housingTypeId;
    }

    /**
     * Set vegetarian
     *
     * @param boolean $vegetarian
     * @return ScymAttender
     */
    public function setVegetarian($vegetarian)
    {
        $this->vegetarian = $vegetarian;

        return $this;
    }

    /**
     * Get vegetarian
     *
     * @return boolean
     */
    public function getVegetarian()
    {
        return $this->vegetarian;
    }

    /**
     * Set attended
     *
     * @param boolean $attended
     * @return ScymAttender
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
     * Set generationId
     *
     * @param int $generationId
     * @return ScymAttender
     */
    public function setGenerationId($generationId)
    {
        $this->generationId = $generationId;

        return $this;
    }

    /**
     * Get generationId
     *
     * @return int
     */
    public function getGenerationId()
    {
        return $this->generationId;
    }

    /**
     * Set gradelevel
     *
     * @param string $gradelevel
     * @return ScymAttender
     */
    public function setGradeLevel($gradelevel)
    {
        $this->gradeLevel = $gradelevel;

        return $this;
    }

    /**
     * Get gradelevel
     *
     * @return string
     */
    public function getGradeLevel()
    {
        return $this->gradeLevel;
    }

    /**
     * Set agegroup
     *
     * @param integer $agegroup
     * @return ScymAttender
     */
    public function setAgeGroupId($agegroup)
    {
        $this->ageGroupId = $agegroup;

        return $this;
    }

    /**
     * Get agegroup
     *
     * @return integer
     */
    public function getAgeGroupId()
    {
        return $this->ageGroupId;
    }

    /**
     * Set creditTypeId
     *
     * @param integer $creditTypeId
     * @return ScymAttender
     */
    public function setCreditTypeId($creditTypeId)
    {
        $this->creditTypeId = $creditTypeId;

        return $this;
    }

    /**
     * Get feeCreditId
     *
     * @return integer
     */
    public function getCreditTypeId()
    {
        return $this->creditTypeId;
    }

    /**
     * Set singleoccupant
     *
     * @param boolean $singleoccupant
     * @return ScymAttender
     */
    public function setSingleOccupant($singleoccupant)
    {
        $this->singleOccupant = $singleoccupant;

        return $this;
    }

    /**
     * Get singleoccupant
     *
     * @return boolean
     */
    public function getSingleOccupant()
    {
        return $this->singleOccupant;
    }

    /**
     * Set glutenfree
     *
     * @param boolean $glutenfree
     * @return ScymAttender
     */
    public function setGlutenfree($glutenfree)
    {
        $this->glutenFree = $glutenfree;

        return $this;
    }

    /**
     * Get glutenfree
     *
     * @return boolean
     */
    public function getGlutenfree()
    {
        return $this->glutenFree;
    }

    /**
     * Set registration
     *
     * @param ScymRegistration $registration
     * @return ScymAttender
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

    private function assignDob($dateString) {
        try {
            $dateValue = empty($dateString) ? null : new \DateTime($dateString);
            $this->dateOfBirth = $dateValue;
        }
        catch(\Exception $ex) {
            return false;
        }
        return true;
    }


    public function updateFromDataTransferObject(AttenderDto $dto)
    {
        $this->attenderId            =  $dto->getAttenderId();
        $this->firstName             =  $dto->getFirstName();
        $this->lastName              =  $dto->getLastName();
        $this->middleName            =  $dto->getMiddleName();
        $this->affiliationCode       =  $dto->getAffiliationCode();
        $this->otherAffiliation      =  $dto->getOtherAffiliation();
        $this->firstTimer            =  $dto->getFirstTimer();
        $this->notes                 =  $dto->getNotes();
        $this->linens                =  $dto->getLinens();
        $this->specialNeedsTypeId    =  $dto->getSpecialNeedsTypeId();
        $this->arrivalTime           =  $dto->getArrivalTime();
        $this->departureTime         =  $dto->getDepartureTime();
        $this->housingTypeId         =  $dto->getHousingTypeId();
        $this->vegetarian            =  $dto->getVegetarian();
        $this->attended              =  $dto->getAttended();
        $this->generationId          =  $dto->getGenerationId();
        $this->gradeLevel            =  $dto->getGradeLevel();
        $this->ageGroupId            =  $dto->getAgeGroupId();
        $this->creditTypeId          =  $dto->getCreditTypeId();
        $this->singleOccupant        =  $dto->getSingleOccupant();
        $this->glutenFree            =  $dto->getGlutenFree();

        return true;
    }

    public function getDataTransferObject()
    {
        $result = new \stdClass();
        $result->attenderId            =  $this->attenderId;
        $result->registrationId        =  $this->getRegistrationId();
        $result->firstName             =  $this->firstName;
        $result->lastName              =  $this->lastName;
        $result->middleName            =  $this->middleName;
        $result->dateOfBirth           =  $this->dateOfBirth;
        $result->affiliationCode       =  $this->affiliationCode;
        $result->otherAffiliation      =  $this->otherAffiliation;
        $result->firstTimer            =  $this->firstTimer;
        $result->teacher               =  $this->teacher;
        $result->financialAidRequested =  $this->financialAidRequested;
        $result->guest                 =  $this->guest;
        $result->notes                 =  $this->notes;
        $result->linens                =  $this->linens;
        $result->specialNeedsTypeId    =  $this->specialNeedsTypeId;
        $result->arrivalTime           =  $this->arrivalTime;
        $result->departureTime         =  $this->departureTime;
        $result->housingTypeId         =  $this->housingTypeId;
        $result->vegetarian            =  $this->vegetarian;
        $result->attended              =  $this->attended;
        $result->generationId          =  $this->generationId;
        $result->gradeLevel            =  $this->gradeLevel;
        $result->ageGroupId            =  $this->ageGroupId;
        $result->creditTypeId          =  $this->creditTypeId;
        $result->singleOccupant        =  $this->singleOccupant;
        $result->glutenFree            =  $this->glutenFree;

        return $result;
    }

    public static function CreateAttender(AttenderDto $dto) {
        $result = new ScymAttender();
        $result->updateFromDataTransferObject($dto);
        $meals = $dto->getMeals();
        if ($meals !== null) {
           $result->addMeals($meals);
        }
        return $result;
    }

    public function addNewMeal($mealtime)
    {
        $meal = new ScymMeal();
        $meal->setMealtime($mealtime);
        $this->addMeal($meal);
    }

    public function addMeals(array $mealtimes) {
        if ($mealtimes !== null) {
            foreach ($mealtimes as $mealtime) {
                if (!$this->meals->contains($mealtime)) {
                    $this->addNewMeal($mealtime);
                }
            }
        }
    }


    public function updateMeals(array $mealtimes) {
        $meals = $this->meals->toArray();
        $currentMeals = array();

        $removed = array();
        foreach ($meals as $meal) {
            /**
             * @var $meal ScymMeal
             */
            if (!in_array($meal->getMealtime(),$mealtimes)) {
                $this->meals->removeElement($meal);
                array_push($removed,$meal);
            }
            else {
                array_push($currentMeals,$meal->getMealtime());
            }
        }
        foreach ($mealtimes as $mealtime) {
            if (!in_array($mealtime,$currentMeals)) {
                $this->addNewMeal($mealtime);
            }
        }
        return $removed;
    }

    public function getFullName() {
        $result = $this->firstName ? trim($this->firstName) : '';
        $middle = $this->middleName ? trim($this->middleName) : '';
        $last = $this->lastName ? trim($this->lastName) : '';
        if ($this->middleName) {
            $result .=  $result ? " $this->middleName" : $this->middleName;
        }
        if ($this->lastName) {
            $result .=  $result ? " $this->lastName" : $this->lastName;
        }
        return $result;
    }

}
