<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangeInterface;

/**
 * Description of PlaceChange
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceChange implements ChangeInterface{
    
    private $type;
    private $value = null;
    
    public function __construct($type, $newValue = null)
    {
        $this->type = $type;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getNewValue()
    {
        return $this->value;
    }
}

