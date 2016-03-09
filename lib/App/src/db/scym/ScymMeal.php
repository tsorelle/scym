<?php

namespace App\db\scym;
use App\db\scym\ScymAttender;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymMeal
 *
 * @Table(name="meals", indexes={@Index(name="meals_attender_fk", columns={"attenderID"})})
 * @Entity
 */
class ScymMeal
{
    /**
     * @var integer
     *
     * @Column(name="mealID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $mealid;

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
     * @var int
     *
     * @Column(name="mealTime", type="integer", nullable=false)
     */
    private $mealtime = '0';

    /**
     * @var boolean
     *
     * @Column(name="vegetarian", type="boolean", nullable=false)
     */
    private $vegetarian = '0';


    /**
     * Get mealid
     *
     * @return integer 
     */
    public function getMealid()
    {
        return $this->mealid;
    }

    /**
     * Set mealtime
     *
     * @param int $mealtime
     * @return ScymMeal
     */
    public function setMealtime($mealtime)
    {
        $this->mealtime = $mealtime;

        return $this;
    }

    /**
     * Get mealtime
     *
     * @return int
     */
    public function getMealtime()
    {
        return $this->mealtime;
    }

    /**
     * Set vegetarian
     *
     * @param boolean $vegetarian
     * @return ScymMeal
     */
    public function setVegetarian($vegetarian)
    {
        $this->vegetarian = $vegetarian;

        return $this;
    }

    /**
     * Get vegetarian
     *
     * @return boolean 
     */
    public function getVegetarian()
    {
        return $this->vegetarian;
    }

}
