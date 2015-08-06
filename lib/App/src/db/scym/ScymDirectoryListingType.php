<?php


namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * Directorylistingtypes
 *
 * @Table(name="directorylistingtypes")
 * @Entity
 */
class ScymDirectoryListingType
{
    /**
     * @var integer
     *
     * @Column(name="listingTypeId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $listingtypeid;

    /**
     * @var string
     *
     * @Column(name="description", type="string", length=20, nullable=true)
     */
    private $description;


    /**
     * Get listingtypeid
     *
     * @return integer 
     */
    public function getListingtypeid()
    {
        return $this->listingtypeid;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ScymDirectoryListingType
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
