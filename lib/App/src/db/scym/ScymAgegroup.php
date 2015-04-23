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
class ScymAgegroup
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
     * Get agegroupid
     *
     * @return integer 
     */
    public function getAgegroupid()
    {
        return $this->agegroupid;
    }

    /**
     * Set groupname
     *
     * @param string $groupname
     * @return ScymAgegroup
     */
    public function setGroupname($groupname)
    {
        $this->groupname = $groupname;

        return $this;
    }

    /**
     * Get groupname
     *
     * @return string 
     */
    public function getGroupname()
    {
        return $this->groupname;
    }

    /**
     * Set cutoffage
     *
     * @param integer $cutoffage
     * @return ScymAgegroup
     */
    public function setCutoffage($cutoffage)
    {
        $this->cutoffage = $cutoffage;

        return $this;
    }

    /**
     * Get cutoffage
     *
     * @return integer 
     */
    public function getCutoffage()
    {
        return $this->cutoffage;
    }
}
