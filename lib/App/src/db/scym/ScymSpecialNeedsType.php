<?php

namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymSpecialNeedsType
 *
 * @Table(name="specialneedstypes")
 * @Entity
 */
class ScymSpecialNeedsType
{
    /**
     * @var integer
     *
     * @Column(name="specialNeedsTypeID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $specialneedstypeid = '0';

    /**
     * @var string
     *
     * @Column(name="specialNeedsCode", type="string", length=20, nullable=true)
     */
    private $specialneedscode;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=100, nullable=false)
     */
    private $description = '';

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


    /**
     * Get specialneedstypeid
     *
     * @return integer 
     */
    public function getSpecialNeedsTypeId()
    {
        return $this->specialneedstypeid;
    }

    /**
     * Set specialneedscode
     *
     * @param string $specialneedscode
     * @return ScymSpecialNeedsType
     */
    public function setSpecialNeedsCode($specialneedscode)
    {
        $this->specialneedscode = $specialneedscode;

        return $this;
    }

    /**
     * Get specialneedscode
     *
     * @return string 
     */
    public function getSpecialNeedsCode()
    {
        return $this->specialneedscode;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymSpecialNeedsType
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
     * Set active
     *
     * @param boolean $active
     * @return ScymSpecialNeedsType
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
}
