<?php

namespace App\db\scym;


use Doctrine\ORM\Mapping as ORM;

/**
 * ScymHousingType
 *
 * @Table(name="housingtypes")
 * @Entity
 */
class ScymHousingType
{
    /**
     * @var integer
     *
     * @Column(name="housingTypeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $housingTypeId = '0';

    /**
     * @var string
     *
     * @Column(name="housingTypeCode", type="string", length=20, nullable=false)
     */
    private $housingTypeCode = '';

    /**
     * @var string
     *
     * @Column(name="housingTypeDescription", type="string", length=50, nullable=true)
     */
    private $housingTypeDescription;

    /**
     * @var integer
     *
     * @Column(name="category", type="integer", nullable=true)
     */
    private $category = '1';

    /**
     * @var integer
     *
     * @Column(name="active", type="integer", nullable=true)
     */
    private $active = '1';


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
     * Set housingTypeCode
     *
     * @param string $housingTypeCode
     * @return ScymHousingType
     */
    public function setHousingTypeCode($housingTypeCode)
    {
        $this->housingTypeCode = $housingTypeCode;

        return $this;
    }

    /**
     * Get housingTypeCode
     *
     * @return string 
     */
    public function getHousingTypeCode()
    {
        return $this->housingTypeCode;
    }

    /**
     * Set housingTypeDescription
     *
     * @param string $housingTypeDescription
     * @return ScymHousingType
     */
    public function setHousingTypeDescription($housingTypeDescription)
    {
        $this->housingTypeDescription = $housingTypeDescription;

        return $this;
    }

    /**
     * Get housingTypeDescription
     *
     * @return string 
     */
    public function getHousingTypeDescription()
    {
        return $this->housingTypeDescription;
    }

    /**
     * Set category
     *
     * @param integer $category
     * @return ScymHousingType
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return integer 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return ScymHousingType
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive()
    {
        return $this->active;
    }
}
