<?php

namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use Tops\sys\TDateTime;

/**
 * ScymAnnualSession
 *
 * @Table(name="annualsessions")
 * @Entity
 */
class ScymAnnualSession
{
    /**
     * @var string
     *
     * @Column(name="year", type="string", length=4, nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @Column(name="start", type="date", nullable=true)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @Column(name="end", type="date", nullable=true)
     */
    private $end;

    /**
     * @var \DateTime
     *
     * @Column(name="deadline", type="date", nullable=true)
     */
    private $deadline;



    /**
     * @var string
     *
     * @Column(name="location", type="string", length=100, nullable=true)
     */
    private $location;


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
     * Set start
     *
     * @param \DateTime $start
     * @return ScymAnnualSession
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return ScymAnnualSession
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set Deadline
     *
     * @param \DateTime $Deadline
     * @return ScymAnnualSession
     */
    public function setDeadline($value)
    {
        $this->deadline = $value;

        return $this;
    }

    /**
     * Get Deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }


    /**
     * Set location
     *
     * @param string $location
     * @return ScymAnnualSession
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    public function toDataTransferObject() {
        $result = new \stdClass();
        $result->year =  $this->year;
        $result->startDate = TDateTime::format($this->start,'Y-m-d');
        $result->endDate = TDateTime::format($this->end,'Y-m-d');
        $result->datesText = TDateTime::formatRange($this->start,$this->end);
        $result->location = $this->location;
        $result->deadline = $this->deadline;
        return $result;
    }

}
