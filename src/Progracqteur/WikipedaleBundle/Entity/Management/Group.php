<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\Group as BaseGroup;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Group
 */
class Group extends BaseGroup
{

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Zone $polygon
     */
    private $Zone;

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Notation
     */
    private $notation;
    

 //   private ;
    

    public function __construct($name = '', $roles = array()) {
        parent::__construct($name, $roles);
        $this->addRole('ROLE_NOTATION');
    }
    
    /**
     * Set Zone
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\Zone $Zone
     * @return Group
     */
    public function setZone(Zone $Zone = null)
    {
        $this->Zone = $Zone;
        return $this;
    }
    
    public function getZone()
    {
        return $this->Zone;
    }

    
    

    /**
     * Set notation
     *
     * @param Progracqteur\WikipedaleBundle\Management\Notation $notation
     * @return Group
     */
    public function setNotation(\Progracqteur\WikipedaleBundle\Entity\Management\Notation $notation = null)
    {
        $this->notation = $notation;
        return $this;
    }

    /**
     * Get notation
     *
     * @return Progracqteur\WikipedaleBundle\Management\Notation 
     */
    public function getNotation()
    {
        return $this->notation;
    }
    
    public function __toString() {
        return $this->getName().' ("'.$this->getNotation().'" à '.$this->getZone().')';
    }
}