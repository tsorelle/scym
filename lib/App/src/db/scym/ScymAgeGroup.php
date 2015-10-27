<?php
namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;

/**
 * Agegroups
 *
 * @Table(name="agegroups")
 * @Entity
 */
class ScymAgeGroup
{
    /**
     * @var integer
     *
     * @Column(name="ageGroupId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $agegroupid = '0';

    /**
     * @var string
     *
     * @Column(name="groupName", type="string", length=20, nullable=true)
     */
    private $groupname;

    /**
     * @var integer
     *
     * @Column(name="cutoffAge", type="integer", nullable=true)
     */
    private $cutoffage;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


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
     * Set groupname
     *
     * @param string $groupname
     * @return ScymAgeGroup
     */
    public function setGroupName($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupname;
    }

    /**
     * Set cutoffage
     *
     * @param integer $cutoffage
     * @return ScymAgeGroup
     */
    public function setCutoffAge($cutoffage)
    {
        $this->cutoffage = $cutoffage;

        return $this;
    }

    /**
     * Get cutoffage
     *
     * @return integer
     */
    public function getCutoffAge()
    {
        return $this->cutoffage;
    }
    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymAgeGroup
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
