<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

/**
 * Type of the place
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceType {
    
    /**
     *
     * @var int 
     */
    private $id;
    
    /**
     *
     * @var string 
     */
    private $label;
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 
     * @return string
     */
    public function getLabel() 
    {
        return $this->label;
    }
    
    /**
     * 
     * @param string $label
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}

