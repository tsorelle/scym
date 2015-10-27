<?php

namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use Tops\sys\TDateTime;

/**
 * ScymSessionDate
 *
 * @Table(name="ymdates")
 * @Entity
 */
class ScymSessionDate
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
     * @return ScymSessionDate
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
     * @return ScymSessionDate
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

    public function toDataTransferObject() {
        $result = new \stdClass();
        $result->year =  $this->year;
        $result->startDate = TDateTime::format($this->start,'Y-m-d');
        $result->endDate = TDateTime::format($this->end,'Y-m-d');
        $result->displayText = TDateTime::formatRange($this->start,$this->end);
        return $result;
    }
}
