<?php


namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScymHousingAssignment
 *
 * @Table(name="housingassignments", uniqueConstraints={@UniqueConstraint(name="personDay", columns={"attenderID", "day"})}, indexes={@Index(name="housingassignments_attender_fk", columns={"attenderID"})})
 * @Entity
 */
class ScymHousingAssignment
{
    /**
     * @var integer
     *
     * @Column(name="housingAssignmentId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $housingAssignmentId;

    /**
     * @var ScymAttender
     *
     * @ManyToOne(targetEntity="ScymAttender",inversedBy="meals")
     * @JoinColumn(name="attenderID", referencedColumnName="attenderID")
     */
    protected $attender;

    /**
     * Set attender
     *
     * @param ScymAttender $attender
     * @return ScymMeal
     */
    public function setAttender(ScymAttender $attender = null)
    {
        $this->attender = $attender;

        return $this;
    }

    /**
     * Get attender
     *
     * @return ScymAttender
     */
    public function getAttender()
    {
        return $this->attender;
    }

    public function getAttenderId()
    {
        return $this->attender ? $this->attender->getAttenderid() : null;
    }



    /**
     * @var integer
     *
     * @Column(name="day", type="integer", nullable=true)
     */
    private $day;

    /**
     * @var integer
     *
     * @Column(name="housingUnitId", type="integer", nullable=true)
     */
    private $housingUnitId;

    /**
     * @var string
     *
     * @Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @Column(name="confirmed", type="boolean", nullable=true)
     */
    private $confirmed = '0';


    /**
     * Get housingAssignmentId
     *
     * @return integer 
     */
    public function getHousingAssignmentId()
    {
        return $this->housingAssignmentId;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return ScymHousingAssignment
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set housingUnitId
     *
     * @param integer $housingUnitId
     * @return ScymHousingAssignment
     */
    public function setHousingUnitId($housingUnitId)
    {
        $this->housingUnitId = $housingUnitId;

        return $this;
    }

    /**
     * Get housingUnitId
     *
     * @return integer 
     */
    public function getHousingUnitId()
    {
        return $this->housingUnitId;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ScymHousingAssignment
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

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     * @return ScymHousingAssignment
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

    public static function CreateAssignment($day,$unitId) {
        $result = new ScymHousingAssignment();
        $result->setDay($day);
        $result->setHousingUnitId($unitId);
        return $result;
    }
}
