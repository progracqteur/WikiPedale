<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangeInterface;

/**
 * The elements of the place which has been changed, with the type and the new
 * values. 
 * 
 * Types are stored in instances of
 *    Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceChange implements ChangeInterface{
    
    private $type;
    private $value = null;
    
    public function __construct($type, $newValue = null)
    {
        $this->type = $type;
        $this->value = $newValue;
    }
    
    /**
     * The type of the change.
     * 
     * Types are stored in Progracqteur\WikipedaleBundle\Resources\Security\ChangeService
     * 
     * @return int
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * Return the new value
     * 
     * If the type is :
     * - PLACE_STATUS : the new value is an instance of Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus
     * - PLACE_PHOTO: a string which represent the filename of the new picture
     * - PLACE_GEOM: a Progracqteur\WikipedaleBundle\Resources\Geo\Point
     * - PLACE_ADDRESS : Progracqteur\WikipedaleBundle\Resources\Container\Address
     * 
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->value;
    }
}

