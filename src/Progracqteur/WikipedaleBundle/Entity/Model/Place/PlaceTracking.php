<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use \Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;

/**
 * Description of PlaceTracking
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTracking implements ChangesetInterface {
    
    private $id;
    
    private $author;
    
    private $details;
    
    private $types = array();
    
    private $isCreation = false;
    
    private $date;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place 
     */
    private $place;
    
    public function __construct(Place $place)
    {
        $this->details = new Hash;
        $this->place = $place;
        $this->date = new \DateTime();
    }
    
    public function addChange($type, $newValue)
    {
        if (!in_array($type, $this->types))
        {
            //pour le suivi des changements par Security
            $this->types[] = $type;
            
            //pour l'enregistrement dans la base de donnée
            
            switch ($type)
            {
                case ChangeService::PLACE_CREATOR:
                    if ($newValue instanceof UnregisteredUser)
                    {
                        $this->details->{$type} = $newValue->toHash();
                    } else if ($newValue instanceof User)
                    {
                        $this->details->{$type} = $newValue->getId();
                    }
                    break;
                default:
                    $this->details->{$type} = $newValue;
            }
            
            
            
        }
        
        if ($type === ChangeService::PLACE_CREATION)
        {
            $this->isCreation = true;
        }
    }
    
    public function isCreation() {
        return $this->isCreation;
    }
    
    private $proxyAuthor;
    
    public function getAuthor() {
        
        if ($this->proxyAuthor !== null)
        {
            return $this->proxyAuthor;
        }
        
        if ($this->author !== null)
            return $this->author;
        else {
            if ($this->details->has('author')){
                $u = UnregisteredUser::fromHash($this->details->author);
                return $u;
            } else
                return null;
        }
    }
    
    public function setAuthor(User $user) {
        $this->proxyAuthor = $user;
        
        if ($user instanceof UnregisteredUser)
        {
            $this->details->author = $user->toHash();
        } else if ($user instanceof User) {
            $this->author = $user;
        }    
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    
    
    // fonctions pour l'implémentation de Iterable
    private $intTypes = 0;
    
    public function current() {
        $prop = $this->types[$this->intTypes];
        return new PlaceChange($prop);
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
    
    public function valid() {
        return isset($this->types[$this->intTypes]);
    }

}

