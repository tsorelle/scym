<?php
namespace App\db\scym;
use Doctrine\ORM\Mapping as ORM;

/**
 * Generations
 *
 * @Table(name="generations")
 * @Entity
 */
class ScymGeneration
{
    /**
     * @var integer
     *
     * @Column(name="generationId", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $generationid;

    /**
     * @var string
     *
     * @Column(name="generationName", type="string", length=30, nullable=true)
     */
    private $generationname;

    /**
     * @var boolean
     *
     * @Column(name="active", type="boolean", nullable=false)
     */
    private $active = '1';


    /**
     * Get generationid
     *
     * @return integer 
     */
    public function getGenerationId()
    {
        return $this->generationid;
    }

    /**
     * Set generationname
     *
     * @param string $generationname
     * @return ScymGeneration
     */
    public function setGenerationName($generationname)
    {
        $this->generationname = $generationname;

        return $this;
    }

    /**
     * Get generationname
     *
     * @return string 
     */
    public function getGenerationName()
    {
        return $this->generationname;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ScymGeneration
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
