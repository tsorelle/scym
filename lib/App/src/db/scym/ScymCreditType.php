<?php
namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymCreditType
 *
 * @Table(name="credittypes")
 * @Entity
 */
class ScymCreditType
{
    /**
     * @var integer
     *
     * @Column(name="creditTypeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $credittypeid;

    /**
     * @var string
     *
     * @Column(name="creditTypeCode", type="string", length=20, nullable=true)
     */
    private $credittypecode;



    /**
     * @var string
     *
     * @Column(name="creditTypeName", type="string", length=30, nullable=true)
     */
    private $credittypename;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


    /**
     * Get credittypeid
     *
     * @return integer 
     */
    public function getCreditTypeid()
    {
        return $this->credittypeid;
    }

    /**
     * Get credittypecode
     *
     * @return integer
     */
    public function getCreditTypeCode()
    {
        return $this->credittypecode;
    }

    /**
     * Set credittypecode
     *
     * @param string $value
     * @return ScymCreditType
     */
    public function setCreditTypeCode($value)
    {
        $this->credittypecode = $value;

        return $this;
    }


    /**
     * Set credittype
     *
     * @param string $credittype
     * @return ScymCreditType
     */
    public function setCreditTypeName($credittype)
    {
        $this->credittypename = $credittype;

        return $this;
    }

    /**
     * Get credittype
     *
     * @return string 
     */
    public function getCreditTypeName()
    {
        return $this->credittypename;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymCreditType
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

    public function getDataTransferObject() {
        $result = new \stdClass();
        $result->creditTypeId    = $this->credittypeid;
        $result->creditTypeCode  = $this->credittypecode;
        $result->creditTypeName  = $this->credittypename;
        return $result;
    }
}
