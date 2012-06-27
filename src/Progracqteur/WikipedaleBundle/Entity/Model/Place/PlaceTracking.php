<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;

/**
 * Description of PlaceTracking
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTracking implements ChangesetInterface {
    
    private $author;
    
    private $changes;
    
    private $position = array();
    private $intPosition = 0;
    
    public function __construct()
    {
        $this->changes = new Hash;
    }
    
    public function addChange($propName, $oldValue)
    {
        $h = new Hash();
        $h->oldValue = $oldValue;
        $this->changes->__set($propName, $h);
        if (!in_array($propName, $this->position))
        {
            $this->position[] = $propName;
        }
    }
    
    public function current() {
        $prop = $this->position[$this->intPosition];
        return new PlaceChange($prop);
    }
    
    public function getAuthor() {
        return $this->author;
    }
    
    public function key() {
        return $this->intPosition;
    }
    
    public function next() {
        $this->intPosition++;
    }
    
    public function rewind() {
        $this->intPosition = 0;
    }
    
    public function setAuthor(User $user) {
        $this->author = $user;
    }
    
    public function valid() {
        return isset($this->position[$this->intPosition]);
    }
}

