<?php

namespace App\db;

use Doctrine\ORM\Mapping as ORM;
use Tops\sys\IMailbox;

/**
 * Mailboxes
 *
 * A doctrine entity object that maps the SCYM mailboxes table to the IMailbox interface.
 *
 * @Table(name="mailboxes", uniqueConstraints={@UniqueConstraint(name="boxIndex", columns={"box"})})
 * @Entity
 */
class ScymMailbox implements IMailbox
{
    /**
     * @var integer
     *
     * @Column(name="boxId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $boxId;

    private $isNew;

    /**
     * @var string
     *
     * @Column(name="box", type="string", length=30, nullable=false)
     */
    private $box = '';

    /**
     * @var string
     *
     * @Column(name="address", type="string", length=100, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @Column(name="displayText", type="string", length=100, nullable=true)
     */
    private $displayText;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;


    /**
     * Get mailBoxId
     *
     * @return integer 
     */
    public function getMailboxId()
    {
        if ($this->isNew) {
            return 0;
        }
        return $this->boxId;
    }


    /**
     * Set box
     *
     * @param int $mailBoxId
     */
    public function setMailboxId($mailBoxId) {
        if ($mailBoxId === 0) {
            $this->isNew = true;
        }
        else {
            $this->isNew = false;
            // ignore autoincrement field
        }
    }

    /**
     * Set box
     *
     * @param string $mailBoxCode
     * @return string
     * @internal param string $box
     */
    public function setMailboxCode($mailBoxCode)
    {
        $this->box = $mailBoxCode;

        return $this;
    }

    /**
     * Get box
     *
     * @return string 
     */
    public function getMailboxCode()
    {
        return $this->box;
    }

    /**
     * Set address
     *
     * @param string $email
     * @return string
     */
    public function setEmail($email)
    {
        $this->address = $email;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->address;
    }

    /**
     * Set displaytext
     *
     * @param string $name
     * @return string
     * @internal param string $displaytext
     */
    public function setName($name)
    {
        $this->displayText = $name;

        return $this;
    }

    /**
     * Get displaytext
     *
     * @return string 
     */
    public function getName()
    {
        return $this->displayText;
    }

    /**
     * Set displaytext
     *
     * @param string $description
     * @return string
     * @internal param string $displaytext
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

}
