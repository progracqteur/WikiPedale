<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\Group as BaseGroup;
use Progracqteur\WikipedaleBundle\Entity\Management\City;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Group
 */
class Group extends BaseGroup
{

    /**
     * @var Progracqteur\WikipedaleBundle\Management\City $polygon
     */
    private $city;

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Notation
     */
    private $notation;
    

 //   private ;
    

    public function __construct($name, $roles = array()) {
        parent::__construct($name, $roles);
        $this->addRole('ROLE_NOTATION');
    }
    
    /**
     * Set City
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\City $city
     * @return Group
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;
        return $this;
    }
    
    public function getCity()
    {
        return $this->city;
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
}