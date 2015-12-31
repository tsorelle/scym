<?php

namespace App\db\scym;


use Doctrine\ORM\Mapping as ORM;

/**
 * ScymHousingUnit
 *
 * @Table(name="housingunits")
 * @Entity
 */
class ScymHousingUnit
{
    /**
     * @var integer
     *
     * @Column(name="housingUnitId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $housingUnitId;

    /**
     * @var string
     *
     * @Column(name="unitName", type="string", length=10, nullable=false)
     */
    private $unitName = '';

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @Column(name="capacity", type="integer", nullable=true)
     */
    private $capacity;

    /**
     * @var string
     *
     * @Column(name="housingTypeId", type="integer", nullable=true)
     */
    private $housingTypeId;


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
     * Set unitName
     *
     * @param string $unitName
     * @return ScymHousingUnit
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;

        return $this;
    }

    /**
     * Get unitName
     *
     * @return string 
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymHousingUnit
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
     * Set capacity
     *
     * @param integer $capacity
     * @return ScymHousingUnit
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer 
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set housingTypeId
     *
     * @param integer $housingTypeId
     * @return ScymHousingUnit
     */
    public function setHousingTypeId($housingTypeId)
    {
        $this->housingTypeId = $housingTypeId;

        return $this;
    }

    /**
     * Get housingTypeId
     *
     * @return string 
     */
    public function getHousingTypeId()
    {
        return $this->housingTypeId;
    }
}
