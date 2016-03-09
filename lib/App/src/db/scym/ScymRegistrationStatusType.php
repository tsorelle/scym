<?php
namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymRegistrationStatusType
 *
 * @Table(name="registrationstatustypes")
 * @Entity
 */
class ScymRegistrationStatusType
{
    /**
     * @var integer
     *
     * @Column(name="statusId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $statusid;

    /**
     * @var string
     *
     * @Column(name="statusName", type="string", length=40, nullable=true)
     */
    private $statusname;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


    /**
     * Get statusid
     *
     * @return integer 
     */
    public function getStatusId()
    {
        return $this->statusid;
    }

    /**
     * Set statusname
     *
     * @param string $statusname
     * @return ScymRegistrationStatusType
     */
    public function setStatusName($statusname)
    {
        $this->statusname = $statusname;

        return $this;
    }

    /**
     * Get statusname
     *
     * @return string 
     */
    public function getStatusName()
    {
        return $this->statusname;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymRegistrationStatusType
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
     * @return ScymRegistrationStatusType
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
