<?php

namespace scym\db;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persons
 *
 * @Table(name="persons", indexes={@Index(name="PersonNames", columns={"lastName", "firstName"})})
 * @Entity
 */
class Person
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
     * @Column(name="workPhone", type="string", length=25, nullable=true)
     */
    private $workphone;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @var integer
     *
     * @Column(name="membershipStatus", type="integer", nullable=true)
     */
    private $membershipstatus = '1';

    /**
     * @var integer
     *
     * @Column(name="birthYear", type="integer", nullable=true)
     */
    private $birthyear = '0';

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=30, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @Column(name="password", type="string", length=30, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @Column(name="notes", type="string", length=200, nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @Column(name="dateAdded", type="date", nullable=true)
     */
    private $dateadded;

    /**
     * @var \DateTime
     *
     * @Column(name="dateUpdated", type="date", nullable=true)
     */
    private $dateupdated;

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
     * @Column(name="ymStatusCode", type="integer", nullable=true)
     */
    private $ymstatuscode;

    /**
     * @var integer
     *
     * @Column(name="ymPersonId", type="integer", nullable=true)
     */
    private $ympersonid;

    /**
     * @var string
     *
     * @Column(name="affiliationCode2", type="string", length=30, nullable=true)
     */
    private $affiliationcode2;


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
     * @return Persons
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
     * @return Persons
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
     * @return Persons
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
     * @return Persons
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
     * @return Persons
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
     * Set workphone
     *
     * @param string $workphone
     * @return Persons
     */
    public function setWorkphone($workphone)
    {
        $this->workphone = $workphone;

        return $this;
    }

    /**
     * Get workphone
     *
     * @return string 
     */
    public function getWorkphone()
    {
        return $this->workphone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Persons
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
     * Set membershipstatus
     *
     * @param integer $membershipstatus
     * @return Persons
     */
    public function setMembershipstatus($membershipstatus)
    {
        $this->membershipstatus = $membershipstatus;

        return $this;
    }

    /**
     * Get membershipstatus
     *
     * @return integer 
     */
    public function getMembershipstatus()
    {
        return $this->membershipstatus;
    }

    /**
     * Set birthyear
     *
     * @param integer $birthyear
     * @return Persons
     */
    public function setBirthyear($birthyear)
    {
        $this->birthyear = $birthyear;

        return $this;
    }

    /**
     * Get birthyear
     *
     * @return integer 
     */
    public function getBirthyear()
    {
        return $this->birthyear;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Persons
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
     * Set password
     *
     * @param string $password
     * @return Persons
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Persons
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
     * Set dateadded
     *
     * @param \DateTime $dateadded
     * @return Persons
     */
    public function setDateadded($dateadded)
    {
        $this->dateadded = $dateadded;

        return $this;
    }

    /**
     * Get dateadded
     *
     * @return \DateTime 
     */
    public function getDateadded()
    {
        return $this->dateadded;
    }

    /**
     * Set dateupdated
     *
     * @param \DateTime $dateupdated
     * @return Persons
     */
    public function setDateupdated($dateupdated)
    {
        $this->dateupdated = $dateupdated;

        return $this;
    }

    /**
     * Get dateupdated
     *
     * @return \DateTime 
     */
    public function getDateupdated()
    {
        return $this->dateupdated;
    }

    /**
     * Set junior
     *
     * @param boolean $junior
     * @return Persons
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
     * @return Persons
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
     * @return Persons
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
     * @return Persons
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
     * Set ymstatuscode
     *
     * @param integer $ymstatuscode
     * @return Persons
     */
    public function setYmstatuscode($ymstatuscode)
    {
        $this->ymstatuscode = $ymstatuscode;

        return $this;
    }

    /**
     * Get ymstatuscode
     *
     * @return integer 
     */
    public function getYmstatuscode()
    {
        return $this->ymstatuscode;
    }

    /**
     * Set ympersonid
     *
     * @param integer $ympersonid
     * @return Persons
     */
    public function setYmpersonid($ympersonid)
    {
        $this->ympersonid = $ympersonid;

        return $this;
    }

    /**
     * Get ympersonid
     *
     * @return integer 
     */
    public function getYmpersonid()
    {
        return $this->ympersonid;
    }

    /**
     * Set affiliationcode2
     *
     * @param string $affiliationcode2
     * @return Persons
     */
    public function setAffiliationcode2($affiliationcode2)
    {
        $this->affiliationcode2 = $affiliationcode2;

        return $this;
    }

    /**
     * Get affiliationcode2
     *
     * @return string 
     */
    public function getAffiliationcode2()
    {
        return $this->affiliationcode2;
    }
}
