<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Notation
 */
class Notation
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;
    
    public function __construct($id)
    {
        $this->setId($id);
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return Notation
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Notation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}