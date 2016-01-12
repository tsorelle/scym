<?php

namespace App\db\scym;


use Doctrine\ORM\Mapping as ORM;
use App\db\DateStampedEntity;
use App\db\scym\ScymAttender;

/**
 * ScymYouth
 *
 * @Table(name="youths")
 * @Entity @HasLifecycleCallbacks
 */
class ScymYouth extends DateStampedEntity
{
    /**
     * @OneToOne(targetEntity="ScymAttender", mappedBy="youth")
     * @JoinColumn(name="attenderId", referencedColumnName="attenderID")
     */
    protected $attender;


    /**
     * @var integer
     *
     * @Column(name="youthId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $youthid;

    /**
     * @var integer
     *
     * @Column(name="attenderId", type="integer", nullable=false)
    private $attenderid;
     */

    /**
     * @var \DateTime
     *
     * @Column(name="dateOfBirth", type="date", nullable=true)
     */
    private $dateofbirth;

    /**
     * @var boolean
     *
     * @Column(name="generationId", type="boolean", nullable=true)
     */
    private $generationid;

    /**
     * @var string
     *
     * @Column(name="gradeLevel", type="string", length=2, nullable=true)
     */
    private $gradelevel;

    /**
     * @var integer
     *
     * @Column(name="ageGroupId", type="integer", nullable=true)
     */
    private $agegroupid;

    /**
     * @var string
     *
     * @Column(name="sponsor", type="string", length=200, nullable=true)
     */
    private $sponsor;

    /**
     * @var boolean
     *
     * @Column(name="formsSubmitted", type="boolean", nullable=true)
     */
    private $formssubmitted;

    /**
     * @var string
     *
     * @Column(name="notes", type="text", nullable=true)
     */
    private $notes;


    /**
     * Get youthid
     *
     * @return integer 
     */
    public function getYouthId()
    {
        return $this->youthid;
    }

    /**
     * Set dateofbirth
     *
     * @param \DateTime $dateofbirth
     * @return ScymYouth
     */
    public function setDateofBirth($dateofbirth)
    {
        $this->dateofbirth = $dateofbirth;

        return $this;
    }

    /**
     * Get dateofbirth
     *
     * @return \DateTime 
     */
    public function getDateofBirth()
    {
        return $this->dateofbirth;
    }

    /**
     * Set generationid
     *
     * @param boolean $generationid
     * @return ScymYouth
     */
    public function setGenerationId($generationid)
    {
        $this->generationid = $generationid;

        return $this;
    }

    /**
     * Get generationid
     *
     * @return boolean 
     */
    public function getGenerationId()
    {
        return $this->generationid;
    }

    /**
     * Set gradelevel
     *
     * @param string $gradelevel
     * @return ScymYouth
     */
    public function setGradeLevel($gradelevel)
    {
        $this->gradelevel = $gradelevel;

        return $this;
    }

    /**
     * Get gradelevel
     *
     * @return string 
     */
    public function getGradeLevel()
    {
        return $this->gradelevel;
    }

    /**
     * Set agegroupid
     *
     * @param integer $agegroupid
     * @return ScymYouth
     */
    public function setAgeGroupId($agegroupid)
    {
        $this->agegroupid = $agegroupid;

        return $this;
    }

    /**
     * Get agegroupid
     *
     * @return integer 
     */
    public function getAgeGroupId()
    {
        return $this->agegroupid;
    }

    /**
     * Set sponsor
     *
     * @param string $sponsor
     * @return ScymYouth
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * Get sponsor
     *
     * @return string 
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * Set formssubmitted
     *
     * @param boolean $formssubmitted
     * @return ScymYouth
     */
    public function setFormsSubmitted($formssubmitted)
    {
        $this->formssubmitted = $formssubmitted;

        return $this;
    }

    /**
     * Get formssubmitted
     *
     * @return boolean 
     */
    public function getFormsSubmitted()
    {
        return $this->formssubmitted;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ScymYouth
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
     * @param \App\db\scym\ScymAttender $attender
     */
    public function setAttender($attender) {
        $this->attender = $attender;
    }

    /**
     * @return \App\db\scym\ScymAttender
     */
    public function getAttender()
    {
        return $this->attender;
    }

    public function setAttenderId($value) {
        // $this->attenderid = $value;
    }


}
