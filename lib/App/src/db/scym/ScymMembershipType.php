<?php


namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymMembershipType
 *
 * @Table(name="membershiptypes")
 * @Entity
 */
class ScymMembershipType
{
    /**
     * @var integer
     *
     * @Column(name="membershipTypeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $membershiptypeid = '0';

    /**
     * @var string
     *
     * @Column(name="membershipTypeName", type="string", length=40, nullable=true)
     */
    private $membershiptypename;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


    /**
     * Get membershiptypeid
     *
     * @return integer 
     */
    public function getMembershiptypeid()
    {
        return $this->membershiptypeid;
    }

    /**
     * Set membershiptypename
     *
     * @param string $membershiptypename
     * @return ScymMembershipType
     */
    public function setMembershiptypename($membershiptypename)
    {
        $this->membershiptypename = $membershiptypename;

        return $this;
    }

    /**
     * Get membershiptypename
     *
     * @return string 
     */
    public function getMembershiptypename()
    {
        return $this->membershiptypename;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymMembershipType
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
