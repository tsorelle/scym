<?php


namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Addresstypes
 *
 * @Table(name="addresstypes")
 * @Entity
 */
class ScymAddresstype
{
    /**
     * @var integer
     *
     * @Column(name="addressTypeID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $addresstypeid = '0';

    /**
     * @var string
     *
     * @Column(name="addressTypeCode", type="string", length=20, nullable=true)
     */
    private $addresstypecode;

    /**
     * @var string
     *
     * @Column(name="addressTypeDescription", type="string", length=80, nullable=true)
     */
    private $addresstypedescription;


    /**
     * Get addresstypeid
     *
     * @return integer 
     */
    public function getAddresstypeid()
    {
        return $this->addresstypeid;
    }

    /**
     * Set addresstypecode
     *
     * @param string $addresstypecode
     * @return ScymAddresstype
     */
    public function setAddresstypecode($addresstypecode)
    {
        $this->addresstypecode = $addresstypecode;

        return $this;
    }

    /**
     * Get addresstypecode
     *
     * @return string 
     */
    public function getAddresstypecode()
    {
        return $this->addresstypecode;
    }

    /**
     * Set addresstypedescription
     *
     * @param string $addresstypedescription
     * @return ScymAddresstype
     */
    public function setAddresstypedescription($addresstypedescription)
    {
        $this->addresstypedescription = $addresstypedescription;

        return $this;
    }

    /**
     * Get addresstypedescription
     *
     * @return string 
     */
    public function getAddresstypedescription()
    {
        return $this->addresstypedescription;
    }
}
