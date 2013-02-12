<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

/**
 * This entity represent the statuses of the places
 * Those are described by : 
 *    - a type ;
 *    - a value ;
 * 
 * the statuses were discussed there : https://github.com/progracqteur/WikiPedale/issues/37
 * 
 * 
 * 
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceStatus {
    
    /**
     * the type of the status
     * @var string
     */
    private $type;
    
    /**
     * the value of the status
     * @var int
     */
    private $value = 0;
    
    /**
     * 
     * @return int
     */
    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * The type of getValue is :
     * if type is ..... type of getValue is ....
     * 
     * PLACE_DETAILS = deprecated!
     * PLACE_DESCRIPTION = string
     * PLACE_ADDRESS = Progracqteur\WikipedaleBundle\Resources\Container\Address;
     * PLACE_GEOM = Progracqteur\WikipedaleBundle\Resources\Geo\Point;
     * PLACE_ADD_COMMENT = not implemented;
     * PLACE_ADD_VOTE = not implemented;
     * PLACE_ADD_PHOTO = string of the filename;
     * PLACE_REMOVE_PHOTO = not implemented;
     * PLACE_STATUS = Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus;
     * PLACE_STATUS_BICYCLE = deprecated;
     * PLACE_STATUS_Zone = deprecated;
     * PLACE_CREATOR = ;
     * PLACE_ACCEPTED = boolean;
     * PLACE_ADD_CATEGORY = array of id of categories;
     * PLACE_REMOVE_CATEGORY = array of id of categories;
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
    
    public function equals(PlaceStatus $status)
    {
        return ($this->getType() ===  $status->getType() 
                && $this->getValue() === $status->getValue());
    }


    
}

