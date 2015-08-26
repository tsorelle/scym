<?php

namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use App\db\DateStampedEntity;

/**
 * Addresses
 *
 * @Table(name="addresses")
 * @Entity @HasLifecycleCallbacks
 */
class ScymAddress extends DateStampedEntity
{
    /**
     * @OneToMany(targetEntity="ScymPerson", mappedBy="address",fetch="EAGER")
     */
    protected $persons;

    /**
     * Add person
     *
     * @param ScymPerson $person
     * @return ScymAddress
     */
    public function addPerson(ScymPerson $person) {
        $this->persons[] = $person;
        $person->setAddress($this);
        return $this;
    }

    public function removePerson(ScymPerson $person) {
        $this->persons->removeElement($person);
    }

    public function __construct() {
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPersons()  {
        return $this->persons;
    }

    /**
     * @var integer
     *
     * @Column(name="addressID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    protected $addressid;


    /**
     * @var integer
     *
     * @Column(name="addressType", type="integer", nullable=true)
     */
    protected $addresstype = '1';

    /**
     * @var string
     *
     * @Column(name="addressName", type="string", length=50, nullable=true)
     */
    protected $addressname;

    /**
     * @var string
     *
     * @Column(name="address1", type="string", length=50, nullable=true)
     */
    protected $address1;

    /**
     * @var string
     *
     * @Column(name="address2", type="string", length=50, nullable=true)
     */
    protected $address2;

    /**
     * @var string
     *
     * @Column(name="city", type="string", length=40, nullable=true)
     */
    protected $city;

    /**
     * @var string
     *
     * @Column(name="state", type="string", length=2, nullable=true)
     */
    protected $state;

    /**
     * @var string
     *
     * @Column(name="postalCode", type="string", length=20, nullable=true)
     */
    protected $postalcode;

    /**
     * @var string
     *
     * @Column(name="country", type="string", length=25, nullable=true)
     */
    protected $country;

    /**
     * @var string
     *
     * @Column(name="phone", type="string", length=25, nullable=true)
     */
    protected $phone;


    /**
     * @var string
     *
     * @Column(name="notes", type="string", length=200, nullable=true)
     */
    protected $notes;


    /**
     * @var boolean
     *
     * @Column(name="newsletter", type="boolean", nullable=true)
     */
    protected $newsletter;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=true)
     */
    protected $active = '1';

    /**
     * @var integer
     *
     * @Column(name="directoryListingTypeId", type="integer", nullable=false)
     */
    protected $directoryListingTypeId = '1';


    /**
     * @var string
     *
     * @Column(name="sortkey", type="string", length=80, nullable=true)
     */
    protected $sortkey;

    /**
     * Get addressid
     *
     * @return integer
     */
    public function getAddressid()
    {
        return $this->addressid;
    }

    /**
     * Set addresstype
     *
     * @param integer $addresstype
     * @return ScymAddress
     */
    public function setAddresstype($addresstype)
    {
        $this->addresstype = $addresstype;

        return $this;
    }

    /**
     * Get addresstype
     *
     * @return integer 
     */
    public function getAddresstype()
    {
        return $this->addresstype;
    }

    /**
     * Set addressname
     *
     * @param string $addressname
     * @return ScymAddress
     */
    public function setAddressname($addressname)
    {
        $this->addressname = $addressname;

        return $this;
    }

    /**
     * Get addressname
     *
     * @return string 
     */
    public function getAddressname()
    {
        return $this->addressname;
    }

    /**
     * Set address1
     *
     * @param string $address1
     * @return ScymAddress
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string 
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set address2
     *
     * @param string $address2
     * @return ScymAddress
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string 
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return ScymAddress
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return ScymAddress
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set postalcode
     *
     * @param string $postalcode
     * @return ScymAddress
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string 
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return ScymAddress
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return ScymAddress
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ScymAddress
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     * @return ScymAddress
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean 
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymAddress
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

    /**
     * Set directorylistingtypeid
     *
     * @param integer $directorylistingtypeid
     * @return ScymAddress
     */
    public function setDirectoryListingTypeId($directorylistingtypeid)
    {
        $this->directoryListingTypeId = $directorylistingtypeid;

        return $this;
    }

    /**
     * Get directorylistingtypeid
     *
     * @return integer
     */
    public function getDirectoryListingTypeId()
    {
        return $this->directoryListingTypeId;
    }



    /**
     * Set sortkey
     *
     * @param string $sortkey
     * @return ScymAddress
     */
    public function setSortkey($sortkey)
    {
        $this->sortkey = $sortkey;

        return $this;
    }

    /**
     * Get sortkey
     *
     * @return string 
     */
    public function getSortkey()
    {
        return $this->sortkey;
    }

    /**
     * @return \stdClass
     */
    public function getDataTransferObject() {
        $result = new \stdClass();

        $result->addressId = $this->addressid;
        $result->addressTypeId = $this->addresstype;
        $result->addressname = $this->addressname;
        $result->address1 = $this->address1;
        $result->address2 = $this->address2;
        $result->city = $this->city;
        $result->state = $this->state;
        $result->postalcode = $this->postalcode;
        $result->country = $this->country;
        $result->phone = $this->phone;
        $result->notes = $this->notes;
        $result->newsletter = $this->newsletter;
        $result->active  = $this->active;
        $result->sortkey = $this->sortkey;
        $result->lastUpdate = $this->lastUpdateAsString();
        $result->id = $this->addressid; // client side id
        $result->directorylistingtypeid = $this->directoryListingTypeId;
        $result->editState = 0; // unchanged

        return $result;
    }
    
    public function updateFromDataTransferObject(\stdClass $dto)
    {
        $this->addressid = $dto->addressId;
        $this->addresstype = $dto->addressTypeId;
        $this->addressname = $dto->addressname;
        $this->address1 = $dto->address1;
        $this->address2 = $dto->address2;
        $this->city = $dto->city;
        $this->state = $dto->state;
        $this->postalcode = $dto->postalcode;
        $this->country = $dto->country;
        $this->phone = $dto->phone;
        $this->notes = $dto->notes;
        $this->newsletter = $dto->newsletter;
        $this->active = $dto->active;
        $this->directorylistingtypeid= $dto->directorylistingtypeid;
        $this->sortkey = $dto->sortkey;
    }


}
