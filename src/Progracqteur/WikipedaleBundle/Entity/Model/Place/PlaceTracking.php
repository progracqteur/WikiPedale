<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of PlaceTracking
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTracking implements ChangesetInterface {
    
    private $author;
    
    private $changes;
    
    private $types = array();
    private $intTypes = 0;
    
    private $isCreation = false;
    
    public function __construct()
    {
        $this->changes = new Hash;
    }
    
    public function addChange($type, $params)
    {
        if (!in_array($type, $this->types))
        {
            $this->types[] = $type;
        }
        
        if ($type === ChangeService::PLACE_CREATION)
        {
            $this->isCreation = true;
        }
    }
    
    public function current() {
        $prop = $this->types[$this->intTypes];
        return new PlaceChange($prop);
    }
    
    public function getAuthor() {
        return $this->author;
    }
    
    public function key() {
        return $this->intTypes;
    }
    
    public function next() {
        $this->intTypes++;
    }
    
    public function rewind() {
        $this->intTypes = 0;
    }
    
    public function setAuthor(User $user) {
        $this->author = $user;
    }
    
    public function valid() {
        return isset($this->types[$this->intTypes]);
    }

    public function isCreation() {
        return $this->isCreation;
    }
}

