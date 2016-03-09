<?php
namespace App\db\scym;

use App\db\api\FeeTypeDto;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScymFee
 *
 * @Table(name="feetypes")
 * @Entity
 */
class ScymFee
{
    /**
     * @var integer
     *
     * @Column(name="feeTypeID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $feetypeid = '0';

    /**
     * @var string
     *
     * @Column(name="feeCode", type="string", length=20, nullable=false)
     */
    private $feecode = '';

    /**
     * @var integer
     *
     * @Column(name="feeCatagoryId", type="integer", nullable=false)
     */
    private $feecatagoryid = '0';

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=50, nullable=false)
     */
    private $description = '';

    /**
     * @var boolean
     *
     * @Column(name="isCredit", type="boolean", nullable=true)
     */
    private $iscredit = '0';

    /**
     * @var string
     *
     * @Column(name="unitAmount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $unitamount = '0.00';

    /**
     * @var string
     *
     * @Column(name="basis", type="string", length=100, nullable=true)
     */
    private $basis;

    /**
     * @var boolean
     *
     * @Column(name="canwaive", type="boolean", nullable=false)
     */
    private $canwaive = '1';


    /**
     * Get feetypeid
     *
     * @return integer
     */
    public function getFeeTypeId()
    {
        return $this->feetypeid;
    }

    /**
     * Set feecode
     *
     * @param string $feecode
     * @return ScymFee
     */
    public function setFeeCode($feecode)
    {
        $this->feecode = $feecode;

        return $this;
    }

    /**
     * Get feecode
     *
     * @return string
     */
    public function getFeeCode()
    {
        return $this->feecode;
    }

    /**
     * Set feecatagoryid
     *
     * @param integer $feecatagoryid
     * @return ScymFee
     */
    public function setFeeCatagoryId($feecatagoryid)
    {
        $this->feecatagoryid = $feecatagoryid;

        return $this;
    }

    /**
     * Get feecatagoryid
     *
     * @return integer
     */
    public function getFeeCatagoryId()
    {
        return $this->feecatagoryid;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymFee
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
     * Set iscredit
     *
     * @param boolean $iscredit
     * @return ScymFee
     */
    public function setIsCredit($iscredit)
    {
        $this->iscredit = $iscredit;

        return $this;
    }

    /**
     * Get iscredit
     *
     * @return boolean
     */
    public function getIsCredit()
    {
        return $this->iscredit;
    }

    /**
     * Set unitamount
     *
     * @param string $unitamount
     * @return ScymFee
     */
    public function setUnitAmount($unitamount)
    {
        $this->unitamount = $unitamount;

        return $this;
    }

    /**
     * Get unitamount
     *
     * @return string
     */
    public function getUnitAmount()
    {
        return $this->unitamount;
    }

    /**
     * Set basis
     *
     * @param string $basis
     * @return ScymFee
     */
    public function setBasis($basis)
    {
        $this->basis = $basis;

        return $this;
    }

    /**
     * Get basis
     *
     * @return string
     */
    public function getBasis()
    {
        return $this->basis;
    }

    /**
     * Set canwaive
     *
     * @param boolean $canwaive
     * @return ScymFee
     */
    public function setCanWaive($canwaive)
    {
        $this->canwaive = $canwaive;

        return $this;
    }

    /**
     * Get canwaive
     *
     * @return boolean
     */
    public function getCanWaive()
    {
        return $this->canwaive;
    }

    /**
     * @return FeeTypeDto
     */
    public function getDataTransferObject()
    {
        $result = new FeeTypeDto();
        $result->feeTypeId     = $this->feetypeid;
        $result->feeCode       = $this->feecode;
        $result->feeCatagoryId = $this->feecatagoryid;
        $result->description   = $this->description;
        $result->isCredit      = $this->iscredit;
        $result->unitAmount    = $this->unitamount;
        $result->basis         = $this->basis ;
        $result->canWaive      = $this->canwaive;
        return $result;
    }
}
