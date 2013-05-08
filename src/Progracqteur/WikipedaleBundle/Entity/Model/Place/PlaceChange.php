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
     * The type of getValue is :
     * if type is ..... type of getValue is ....
     * 
     * 
     * 
     * If the type is :
     * - PLACE_STATUS : the new value is an instance of Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus
     * - PLACE_PHOTO: a string which represent the filename of the new picture
     * - PLACE_GEOM: a Progracqteur\WikipedaleBundle\Resources\Geo\Point
     * - PLACE_ADDRESS : Progracqteur\WikipedaleBundle\Resources\Container\Address
     * - PLACE_DETAILS = deprecated!
     * - PLACE_DESCRIPTION = string
     * - PLACE_GEOM = Progracqteur\WikipedaleBundle\Resources\Geo\Point;
     * - PLACE_ADD_COMMENT = not implemented;
     * - PLACE_ADD_VOTE = not implemented;
     * - PLACE_ADD_PHOTO = string of the filename;
     * - PLACE_REMOVE_PHOTO = not implemented;
     * - PLACE_STATUS_BICYCLE = deprecated;
     * - PLACE_STATUS_Zone = deprecated;
     * - PLACE_CREATOR = this should not happen;
     * - PLACE_ACCEPTED = boolean;
     * - PLACE_ADD_CATEGORY = array of id of categories after the changes were made;
     * - PLACE_REMOVE_CATEGORY = array of id of categories afther the changes were made;
     * - PLACE_PLACETYPE_ALTER = id of the new placetype
     * -PLACE_MANAGER_ADD ou PLACE_MANAGER_ALTER ou PLACE_MANAGER_REMOVE: id of the manager's group
     * 
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->value;
    }
}

