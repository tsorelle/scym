<?php

namespace App\db\scym;


use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;


/**
 * ScymMeeting
 *
 * @Table(name="meetings", uniqueConstraints={@UniqueConstraint(name="affiliationCodeIndex", columns={"affiliationCode"})})
 * @Entity @HasLifecycleCallbacks
 */
class ScymMeeting extends DateStampedEntity
{
    /**
     * @var integer
     *
     * @Column(name="meetingId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $meetingid;

    /**
     * @var string
     *
     * @Column(name="meetingName", type="string", length=100, nullable=true)
     */
    private $meetingname;

    /**
     * @var string
     *
     * @Column(name="state", type="string", length=30, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @Column(name="area", type="string", length=30, nullable=true)
     */
    private $area;

    /**
     * @var string
     *
     * @Column(name="affiliationCode", type="string", length=20, nullable=true)
     */
    private $affiliationcode = '';

    /**
     * @var string
     *
     * @Column(name="worshipTimes", type="string", length=100, nullable=true)
     */
    private $worshiptimes;

    /**
     * @var string
     *
     * @Column(name="worshipLocation", type="string", length=255, nullable=true)
     */
    private $worshiplocation;

    /**
     * @var string
     *
     * @Column(name="url", type="string", length=100, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @Column(name="detailText", type="text", nullable=true)
     */
    private $detailtext;

    /**
     * @var string
     *
     * @Column(name="note", type="string", length=128, nullable=true)
     */
    private $note;

    /**
     * @var integer
     *
     * @Column(name="latitude", type="integer", nullable=true)
     */
    private $latitude;

    /**
     * @var integer
     *
     * @Column(name="longitude", type="integer", nullable=true)
     */
    private $longitude;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=true)
     */
    private $active = '1';
    
    /**
     * @var ScymQuarterlyMeeting
     *
     * @ManyToOne(targetEntity="ScymQuarterlyMeeting",inversedBy="meetings")
     *   @JoinColumn(name="quarterlyMeetingId", referencedColumnName="quarterlyMeetingId")
     */
    protected $quarterlymeeting;

    public function getQuarterlyMeetingId()
    {
        return $this->quarterlymeeting ? $this->quarterlymeeting->getQuarterlyMeetingId() : null;
    }

    /**
     * Set QuarterlyMeeting
     *
     * @param ScymQuarterlyMeeting $QuarterlyMeeting
     * @return ScymMeeting
     */
    public function setQuarterlyMeeting(ScymQuarterlyMeeting $QuarterlyMeeting = null)
    {
        $this->quarterlymeeting = $QuarterlyMeeting;

        return $this;
    }

    /**
     * Get QuarterlyMeeting
     *
     * @return ScymQuarterlyMeeting
     */
    public function getQuarterlyMeeting()
    {
        return $this->quarterlymeeting;
    }
    
    
    /**
     * Set meetingName
     *
     * @param string $meetingname
     * @return ScymMeeting
     */
    public function setMeetingName($meetingname)
    {
        $this->meetingname = $meetingname;

        return $this;
    }

    /**
     * Get meetingname
     *
     * @return string 
     */
    public function getMeetingName()
    {
        return $this->meetingname;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return ScymMeeting
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set area
     *
     * @param string $area
     * @return ScymMeeting
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string 
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set affiliationcode
     *
     * @param string $affiliationcode
     * @return ScymMeeting
     */
    public function setAffiliationcode($affiliationcode)
    {
        $this->affiliationcode = $affiliationcode;

        return $this;
    }

    /**
     * Get affiliationcode
     *
     * @return string 
     */
    public function getAffiliationcode()
    {
        return $this->affiliationcode;
    }

    /**
     * Set worshiptimes
     *
     * @param string $worshiptimes
     * @return ScymMeeting
     */
    public function setWorshiptimes($worshiptimes)
    {
        $this->worshiptimes = $worshiptimes;

        return $this;
    }

    /**
     * Get worshiptimes
     *
     * @return string 
     */
    public function getWorshiptimes()
    {
        return $this->worshiptimes;
    }

    /**
     * Set worshiplocation
     *
     * @param string $worshiplocation
     * @return ScymMeeting
     */
    public function setWorshiplocation($worshiplocation)
    {
        $this->worshiplocation = $worshiplocation;

        return $this;
    }

    /**
     * Get worshiplocation
     *
     * @return string 
     */
    public function getWorshiplocation()
    {
        return $this->worshiplocation;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return ScymMeeting
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set detailtext
     *
     * @param string $detailtext
     * @return ScymMeeting
     */
    public function setDetailtext($detailtext)
    {
        $this->detailtext = $detailtext;

        return $this;
    }

    /**
     * Get detailtext
     *
     * @return string 
     */
    public function getDetailtext()
    {
        return $this->detailtext;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return ScymMeeting
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
     * Set latitude
     *
     * @param integer $latitude
     * @return ScymMeeting
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return integer 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param integer $longitude
     * @return ScymMeeting
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return integer 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
    
    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymMeeting
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

    public function updateFromDataTransferObject($dto) {
        $this->meetingid  = $dto->meetingId;
        $this->meetingname  = $dto->meetingName;
        $this->state  = $dto->state;
        $this->area  = $dto->area;
        $this->affiliationcode  = $dto->affiliationCode;
        $this->worshiptimes  = $dto->worshipTimes;
        $this->worshiplocation  = $dto->worshipLocation;
        $this->url  = $dto->url;
        $this->detailtext  = $dto->detailText;
        $this->note  = $dto->note;
        $this->longitude  = $dto->latitude;
        $this->latitude  = $dto->longitude;
        $this->active  = $dto->active;
        return (!empty($this->meetingname));
        /*
                Deal with these externally
                $this->  = $dto->quarterlyMeetingId;
                $this->  = $dto->email;
                $this->  = $dto->editState;
        */
    }

    public function getDataTransferObject() {
        $result = new \stdClass();

        $result->meetingId = $this->meetingid;
        $result->meetingName  = $this->meetingname;
        $result->state  = $this->state;
        $result->area  = $this->area;
        $result->affiliationCode  = $this->affiliationcode;
        $result->worshipTimes = $this->worshiptimes;
        $result->worshipLocation = $this->worshiplocation;
        $result->url = $this->url;
        $result->detailText = $this->detailtext;
        $result->note = $this->note;
        $result->latitude = $this->latitude;
        $result->longitude = $this->longitude;
        $result->active = $this->active;
        $result->updatedBy = $this->updatedby;
        $result->quarterlyMeetingId = null;
        $result->quarterlyMeetingName = '';
        $result->lastUpdate = $this->lastUpdateAsString();
        $result->editState = 0; // unchanged

        $quarterly = $this->getQuarterlyMeeting();
        if ($quarterly) {
            $result->quarterlyMeetingId = $quarterly->getQuarterlyMeetingId();
            $result->quarterlyMeetingName = $quarterly->getQuarterlyMeetingName();
        }

        // externally assigned
        $result->email = '';
        $result->mailFormLink = '';

        return $result;
    }



}
