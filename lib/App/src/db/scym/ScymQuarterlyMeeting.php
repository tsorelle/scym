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
 * Quarterlymeetings
 *
 * @Table(name="quarterlymeetings")
 * @Entity @HasLifecycleCallbacks
 */
class ScymQuarterlyMeeting extends DateStampedEntity
{
    /**
     * @OneToMany(targetEntity="ScymMeeting", mappedBy="quarterlymeeting",fetch="EAGER")
     */
    protected $meetings;

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMeetings()  {
        return $this->meetings;
    }

    /**
     * Add person
     *
     * @param ScymMeeting $meeting
     * @return ScymAddress
     */
    public function addMeeting(ScymMeeting $meeting) {
        $this->meetings[] = $meeting;
        $meeting->setQuarterlyMeeting($this);
        return $this;
    }

    public function removeMeeting(ScymMeeting $meeting) {
        $this->meetings->removeElement($meeting);
    }

    /**
     * @var integer
     *
     * @Column(name="quarterlyMeetingId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $quarterlymeetingid;

    /**
     * @var string
     *
     * @Column(name="quarterlyMeetingName", type="string", length=50, nullable=true)
     */
    private $quarterlymeetingname;

    /**
     * @var string
     *
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=true)
     */
    private $active = '1';

    /**
     * Get quarterlymeetingid
     *
     * @return integer 
     */
    public function getQuarterlyMeetingId()
    {
        return $this->quarterlymeetingid;
    }

    /**
     * Set quarterlymeetingname
     *
     * @param string $quarterlymeetingname
     * @return ScymQuarterlyMeeting
     */
    public function setQuarterlyMeetingName($quarterlymeetingname)
    {
        $this->quarterlymeetingname = $quarterlymeetingname;

        return $this;
    }

    /**
     * Get quarterlymeetingname
     *
     * @return string 
     */
    public function getQuarterlyMeetingName()
    {
        return $this->quarterlymeetingname;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymQuarterlyMeeting
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

}
