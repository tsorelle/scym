<?php

namespace App\db\scym;

use App\db\DateStampedEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\Table;


/**
 * Persons
 *
 * @Table(name="persons", indexes={@Index(name="PersonNames", columns={"lastName", "firstName"})})
 * @Entity @HasLifecycleCallbacks
 */
class ScymPerson extends DateStampedEntity
{
    /**
     * @var integer
     *
     * @Column(name="personID", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $personid;

    /**
     * @var \DateTime
     *
     * @Column(name="dateAdded", type="datetime", nullable=true)
     */
    private $dateadded;

    /**
     * @var \DateTime
     *
     * @Column(name="dateUpdated", type="datetime", nullable=true)
     */
    private $dateupdated;


    /**
     * @var string
     *
     * @Column(name="firstName", type="string", length=50, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @Column(name="lastName", type="string", length=50, nullable=false)
     */
    private $lastname = '';

    /**
     * @var string
     *
     * @Column(name="middleName", type="string", length=50, nullable=true)
     */
    private $middlename;

    /**
     * @var integer
     *
     * @Column(name="addressID", type="integer", nullable=true)
     */
    private $addressid;

    /**
     * @var string
     *
     * @Column(name="phone", type="string", length=25, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @Column(name="phone2", type="string", length=25, nullable=true)
     */
    private $phone2;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;


    /**
     * @var \DateTime
     *
     * @Column(name="dateOfBirth", type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @Column(name="addedBy", type="string", length=100, nullable=true)
     */
    private $addedby;

    /**
     * @var string
     *
     * @Column(name="updatedBy", type="string", length=100, nullable=true)
     */
    private $updatedby;


    /**
     * @var string
     *
     * @Column(name="username", type="string", length=30, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @Column(name="notes", type="string", length=200, nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @Column(name="junior", type="boolean", nullable=true)
     */
    private $junior = '0';

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=true)
     */
    private $active = '1';

    /**
     * @var string
     *
     * @Column(name="sortkey", type="string", length=80, nullable=true)
     */
    private $sortkey;

    /**
     * @var string
     *
     * @Column(name="affiliationCode", type="string", length=30, nullable=true)
     */
    private $affiliationcode;

    /**
     * @var integer
     *
     * @Column(name="membershipTypeId", type="integer", nullable=true)
     */
    private $membershipTypeId;




    /**
     * Get personid
     *
     * @return integer
     */
    public function getPersonid()
    {
        return $this->personid;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return ScymPerson
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return ScymPerson
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     * @return ScymPerson
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;

        return $this;
    }

    /**
     * Get middlename
     *
     * @return string
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set addressid
     *
     * @param integer $addressid
     * @return ScymPerson
     */
    public function setAddressid($addressid)
    {
        $this->addressid = $addressid;

        return $this;
    }

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
     * Set phone
     *
     * @param string $phone
     * @return ScymPerson
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
     * Set phone2
     *
     * @param string $phone2
     * @return ScymPerson
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone2
     *
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ScymPerson
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     * @return ScymPerson
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set addedby
     *
     * @param string $addedby
     * @return Persons
     */
    public function setAddedBy($addedby)
    {
        $this->addedby = $addedby;

        return $this;
    }

    /**
     * Get addedby
     *
     * @return string
     */
    public function getAddedBy()
    {
        return $this->addedby;
    }

    /**
     * Set updatedby
     *
     * @param string $updatedby
     * @return ScymPerson
     */
    public function setUpdatedBy($updatedby)
    {
        $this->updatedby = $updatedby;

        return $this;
    }

    /**
     * Get updatedby
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedby;
    }


    /**
     * Set username
     *
     * @param string $username
     * @return ScymPerson
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return ScymPerson
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
     * Set junior
     *
     * @param boolean $junior
     * @return ScymPerson
     */
    public function setJunior($junior)
    {
        $this->junior = $junior;

        return $this;
    }

    /**
     * Get junior
     *
     * @return boolean
     */
    public function getJunior()
    {
        return $this->junior;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymPerson
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
     * Set sortkey
     *
     * @param string $sortkey
     * @return ScymPerson
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
     * Set affiliationcode
     *
     * @param string $affiliationcode
     * @return ScymPerson
     */
    public function setAffiliationcode($affiliationcode)
    {
        $this->affiliationcode = $affiliationcode;

        return $this;
    }

    /**
     * Get affiliationcode
     *
     * @return string
     */
    public function getAffiliationcode()
    {
        return $this->affiliationcode;
    }

    /**
     * Set membershipTypeId
     *
     * @param integer $membershipTypeId
     * @return ScymPerson
     */
    public function setMembershipTypeId($membershipTypeId)
    {
        $this->membershipTypeId = $membershipTypeId;

        return $this;
    }

    /**
     * Get membershipTypeId
     *
     * @return integer
     */
    public function getMembershipTypeId()
    {
        return $this->membershipTypeId;
    }

    private function appendName($name, $next) {
        if (empty($name)) {
            $name = '';
        }
        if (!empty($next)) {
            if ($name) {
                $name .= ' ';
            }
            $name .= $next;
        }
        return $name;
    }

    public function getShortName() {
        $name = $this->appendName($this->firstname,$this->lastname);
        if (empty($name)) {
            return $this->username;
        }
        return $name;

    }

    public function getFullName() {
        $name = $this->appendName($this->firstname,$this->middlename);
        $name = $this->appendName($name,$this->lastname);
        if (empty($name)) {
            return $this->username;
        }
        return $name;
    }

    /**
     * Set dateadded
     *
     * @param \DateTime $dateadded
     * @return ScymPerson
     */
    public function setDateAdded($dateadded)
    {
        $this->dateadded = $dateadded;

        return $this;
    }

    /**
     * Get dateadded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateadded;
    }

    /**
     * Set dateupdated
     *
     * @param \DateTime $dateupdated
     * @return ScymPerson
     */
    public function setDateUpdated($dateupdated)
    {
        $this->dateupdated = $dateupdated;

        return $this;
    }

    /**
     * Get dateupdated
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateupdated;
    }

    /** @PrePersist */
    public function doOnPrePersist()
    {
        $this->setDateStamp();
    }

    /** @PreUpdate */
    public function doOnPreUpdate()
    {
        $this->setUpdateStamp();
    }




}
