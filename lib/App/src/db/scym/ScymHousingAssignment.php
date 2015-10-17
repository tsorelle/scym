<?php


namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScymHousingAssignment
 *
 * @Table(name="housingassignments", uniqueConstraints={@UniqueConstraint(name="personDay", columns={"attenderId", "day"})})
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
     * @var integer
     *
     * @Column(name="attenderId", type="integer", nullable=true)
     */
    private $attenderId;

    /**
     * @var integer
     *
     * @Column(name="day", type="integer", nullable=true)
     */
    private $day;

    /**
     * @var integer
     *
     * @Column(name="housingTypeId", type="integer", nullable=true)
     */
    private $housingTypeId;

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
     * Set attenderId
     *
     * @param integer $attenderId
     * @return ScymHousingAssignment
     */
    public function setAttenderId($attenderId)
    {
        $this->attenderId = $attenderId;

        return $this;
    }

    /**
     * Get attenderId
     *
     * @return integer 
     */
    public function getAttenderId()
    {
        return $this->attenderId;
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
     * Set housingTypeId
     *
     * @param integer $housingTypeId
     * @return ScymHousingAssignment
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
}
