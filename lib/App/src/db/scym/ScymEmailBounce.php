<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/28/2015
 * Time: 10:17 AM
 */

namespace App\db\scym;

use Doctrine\ORM\Mapping as ORM;
use App\db\scym\ScymPerson;

/**
 * ScymEmailbounce
 *
 * @Table(name="emailbounces", indexes={@Index(name="bounce_person", columns={"personId"})})
 * @Entity
 */
class ScymEmailBounce
{
    /**
     * @var integer
     *
     * @Column(name="bounceId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $bounceid;

    /**
     * @var string
     *
     * @Column(name="email", type="string", length=200, nullable=false)
     */
    private $email;

    /**
     * @var \Persons
     *
     * @ManyToOne(targetEntity="ScymPerson")
     * @JoinColumns({
     *   @JoinColumn(name="personId", referencedColumnName="personID")
     * })
     */
    private $person;


    /**
     * Get bounceid
     *
     * @return integer
     */
    public function getBounceid()
    {
        return $this->bounceid;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return ScymEmailBounce
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
     * Set person
     *
     * @param ScymPerson $person
     * @return ScymEmailBounce
     */
    public function setPerson(ScymPerson $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return ScymPerson
     */
    public function getPerson()
    {
        return $this->person;
    }

    public function getDataTransferObject() {
        $result = new \stdClass();
        $person = $this->getPerson();
        $result->name = $person->getFullname();
        $result->personId = $person->getPersonId();
        $result->email = $this->getEmail();
        $result->correction = '';
        $result->validation = '';
        return $result;
    }
}