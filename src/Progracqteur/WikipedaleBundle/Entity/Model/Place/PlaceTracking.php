<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model\Place;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use \Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;

/**
 * PlaceTracking store changes on Place instances.
 * 
 * PlaceTracking is iterable: every element of an iteration is an instance of 
 * PlaceChange
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTracking implements ChangesetInterface {
    
    private $id;
    
    private $author;
    
    private $details;
    
    private $types = array();
    private $values = array();
    
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
    
    /**
     * 
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function getPlace()
    {
        return $this->place;
    }
    
    /**
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getIdUUID()
    {
        return dechex($this->getId());
    }
    
    public function addChange($type, $newValue, $options = array())
    {
        if (!in_array($type, $this->types))
        {
            //pour le suivi des changements par Security
            $this->types[] = $type;
            $this->values[] = $newValue;
            
            //pour l'enregistrement dans la base de donnée
            
            if ($this->details->has('changes') === false)
            {
                $this->details->changes = new Hash();
            }
            
            //transformation de newValue si nécessaire
            //et traitement de creation
            switch ($type)
            {
                case ChangeService::PLACE_CREATOR:
                    //Il ne faut rien faire: place creator n'est normalement pas permis
                    break;
                case ChangeService::PLACE_ADD_PHOTO:
                    $newValue = $newValue->getFileName();
                    break;
                case ChangeService::PLACE_CREATION:
                    $this->isCreation = true;
                    //il n'y a pas d'autrs modifs à effectuer
                    break;
                case ChangeService::PLACE_GEOM:
                    $newValue = $newValue->toGeoJson();
                    break;
                case ChangeService::PLACE_ADDRESS:
                    $newValue = json_encode($newValue->toArray());
                    break;
                case ChangeService::PLACE_STATUS:
                    $a = array('type' => $newValue->getType(),
                        'value' => $newValue->getValue());
                    $newValue = json_encode($a);
                    break;
                case ChangeService::PLACE_ADD_CATEGORY:
                case ChangeService::PLACE_REMOVE_CATEGORY:
                    $ids = array();
                    foreach ($newValue as $category)
                    {
                        $ids[]['id'] = $category->getId();
                    }
                    $newValue = json_encode($ids);
                    
                //default:
                    //rien à faire
            }
            
            $this->details->changes->{$type} = $newValue;
            
            
            
        }
        
        
    }
    
    /**
     * return true if the changeset concern a creation of a place.
     * @return boolean
     */
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
    
    
    /**
     * get all the changes into an array of 
     * Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceChange
     * 
     * @return array
     */
    public function getChanges()
    {
        $a = array();
        
        foreach ($this as $changes)
        {
            $a[] = $changes;
        }
        
        return $a;
    }
    
    
    
    // fonctions pour l'implémentation de Iterable
    private $intTypes = 0;
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceChange
     */
    public function current() {
        $prop = $this->types[$this->intTypes];
        $val = $this->values[$this->intTypes];
        return new PlaceChange($prop, $val);
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
        
        if ($this->details->has("changes")=== true && count($this->types) === 0)
        {
            $this->prepareIterationFromHash();
        }
        
        return isset($this->types[$this->intTypes]);
    }
    
    /**
     * this function prepare the class for iteration. It transforms the hash
     * into PlaceChanges elements, ready to be iterated one by one.
     */
    private function prepareIterationFromHash()
    {
        $a = $this->details->changes->toArray();
        
        foreach ($a as $key => $value)
        {
            $this->types[] = $key;
            
            switch ($key)
            {
                case ChangeService::PLACE_CREATOR:
                    //Il ne faut rien faire: place creator n'est normalement pas permis
                    break;
                case ChangeService::PLACE_ADD_PHOTO:
                    $newValue = $value;
                    break;
                case ChangeService::PLACE_CREATION:
                    //nothing to do - this case should not happen
                    break;
                case ChangeService::PLACE_GEOM:
                    $newValue = Point::fromGeoJson($value);
                    break;
                case ChangeService::PLACE_ADDRESS:
                    $a = json_decode($value);
                    $newValue = Address::fromArray($a);
                    break;
                case ChangeService::PLACE_STATUS:
                    $a = json_decode($value); 
                    $status = new PlaceStatus();
                    $status->setType($a->type)->setValue($a->value);
                    $newValue = $status;
                    break;
                case ChangeService::PLACE_ADD_CATEGORY:
                case ChangeService::PLACE_REMOVE_CATEGORY: 
                    $a = json_decode($value);
                    $newValue = $a;
                default:
                    $newValue = $value;
            }

            $this->values[] = $newValue;
        }
    }

}

