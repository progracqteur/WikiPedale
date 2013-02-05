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
    private $zone;

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Notation
     */
    private $notation;
    
    /**
     *
     * @var string 
     */
    private $type;
    
    /**
     * @var string
     */
    const TYPE_NOTATION = 'NOTATION';
    /**
     * @var string
     */
    const TYPE_MANAGER = 'MANAGER';
    /**
     * @var string
     */
    const TYPE_MODERATOR = 'MODERATOR';
    

    public function __construct($name = '', $roles = array()) {
        parent::__construct($name, $roles);
    }
    
    /**
     * Set Zone
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\Zone $Zone
     * @return Group
     */
    public function setZone(Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }
    
    public function getZone()
    {
        return $this->zone;
    }

    public function setType($type) 
    {
        $this->type = $type;
        return $this;
    }
    
    public function getType()
    {
        return $this->type;
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