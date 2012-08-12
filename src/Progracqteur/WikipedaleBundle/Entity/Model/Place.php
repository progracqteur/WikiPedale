<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeableInterface;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Place
 */
class Place implements NormalizableInterface, ChangeableInterface, NotifyPropertyChanged
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Address $address
     */
    private $address;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    private $geom;



    /**
     * @var datetime $createDate
     */
    private $createDate;

    /**
     * @var int $nbVote
     */
    private $nbVote = 0;

    /**
     * @var int $nbComm
     */
    private $nbComm = 0;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     */
    private $infos;
    
    /**
     *
     * @var boolean
     */
    private $accepted = true;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;
    
    private $creatorUnregisteredProxy;
    
    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Photo
     */
    private $photos;
    /**
     * @var string $description
     */
    private $description = '';
    
    private $nbPhoto = 0;
    
    private $statusCity = 0;
    
    private $statusBicycle = 0;
    
    private $lastUpdate;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking 
     */
    private $changeset = null;
    
    private $_listeners = array();

    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $d = new \DateTime();
        $this->setLastUpdate($d);
        $this->setCreateDate($d);
        $this->infos = new Hash();
        $this->address = new Address();
        $this->getChangeset()->addChange(ChangeService::PLACE_CREATION, null);
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    private function change($propName, $oldValue, $newValue) {
/* FIXME: il semble que les chagnements de la description (et de l'adresse ?)
 * ne soient 
 * pas pris en compte lorsque le trackingPolicy est sur Notify
 */
        if ($this->_listeners) {
            foreach ($this->_listeners as $listener) {
                $listener->propertyChanged($this, $propName, $oldValue, $newValue);
            }
            $this->setLastUpdateNow();
        }  
    }

    /**
     * Set address
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Address $adress
     */
    public function setAddress(Address $address)
    {
        if (! $address->equals($this->address))
        {
            $this->change('address', $this->address, $address);
            $this->address = $address;
            $this->getChangeset()->addChange(ChangeService::PLACE_DETAILS, null);
        }
        
    }

    /**
     * Get adress
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getAddress()
    {
        if ($this->address != null) 
        {
            return $this->address;
        } else 
        {
            $addr =  new Address();
            $addr->setRoad('Adresse inconnue');
            return $addr;
        }
    }

    /**
     * Set geom
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    public function setGeom(Point $geom)
    {
        if ($this->getGeom() === null)
        {
            $this->geom = $geom;
            return;
        }
        
        if (
                $this->getGeom()->getLat() != $geom->getLat()
                && $this->getGeom()->getLon() != $geom->getLon()) 
        {
            $this->change('geom', $this->geom, $geom);
            $this->geom = $geom;
            $this->getChangeset()->addChange(ChangeService::PLACE_DETAILS, null);
        }
    }

    /**
     * Get geom
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Geo\Point 
     */
    public function getGeom()
    {
        return $this->geom;
    }




    /**
     * Set createDate
     *
     * @param datetime $createDate
     */
    private function setCreateDate($createDate)
    {
        $this->change('createDate', $this->createDate, $createDate);
        $this->createDate = $createDate;
    }

    /**
     * Get createDate
     *
     * @return datetime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }


    /**
     * Get nbVote
     *
     * @return int 
     */
    public function getNbVote()
    {
        return $this->nbVote;
    }



    /**
     * Get nbComm
     *
     * @return int 
     */
    public function getNbComm()
    {
        return $this->nbComm;
    }

    /**
     * Set infos
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     * @deprecated
     */
    private function setInfos(Hash $infos)
    {
        $this->infos = $infos;
        $this->setLastUpdateNow();
    }

    /**
     * Get infos
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    private function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set creator
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\User $creator
     */
    public function setCreator(\Progracqteur\WikipedaleBundle\Entity\Management\User $creator)
    {
        if ($this->_getCreator() === null OR  !($creator->equals($this->_getCreator())))
        {
            if ($creator instanceof UnregisteredUser)
            {
                if ($this->creator !== null)
                {
                    $this->change('creator', $this->creator, null);
                    $this->creator = null;
                }


                $old = clone($this->getInfos());
                $this->infos->creator = $creator->toHash();
                $this->creatorUnregisteredProxy = $creator;

                $this->change('infos', $old, $this->getInfos());
                $this->getChangeset()->addChange(ChangeService::PLACE_CREATOR, null);

            } else {
                $this->change('creator', $this->creator, $creator);
                $this->creator = $creator;
                $this->getChangeset()->addChange(ChangeService::PLACE_CREATOR, null);
                //TODO : si un unregistreredCreator existe, il faut l'enlever
            }
        }
    }

    /**
     * Get creator
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        return $this->_getCreator();
        
        //TODO : modifier les lignes suivantes, devenues inutiles
        
        if ($creator === null)
        {
            throw new \Exception('Aucun créateur enregistré');
        } else {
            return $creator;
        }
        
        
    }
    
    /**
     * REnvoie le créateur
     * Méthode privée utilisée pour éviter l'exception de la méthode
     * publique getCreator. Utilisée dans setCreator().
     * 
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     * @deprecated
     */
    private function _getCreator()
    {
        if (!is_null($this->creator))
        {
            return $this->creator;
        } 
        elseif (!is_null($this->creatorUnregisteredProxy))
        {
            return $this->creatorUnregisteredProxy;
        } 
        elseif ($this->infos->has('creator'))
        {
            $u = UnregisteredUser::fromHash($this->infos->creator);
            $this->creatorUnregisteredProxy = $u;
            return $u;
        } 
        else 
        {
            return null;
        }
    }

    /**
     * Get photos
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }
    
    public function getNbPhoto()
    {
        return $this->nbPhoto;
    }
    
    public function increaseComment()
    {
        $this->nbComm++;
        $this->change('nbComm', ($this->nbComm -1 ), $this->nbComm);
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_COMMENT, null);
    }
    
    public function increaseVote()
    {
        $this->nbVote++;
        $this->change('nbVote', ($this->nbVote - 1), $this->nbVote);
        $this->getChangeset()->addChange(ChangeService::PLACE_ADD_VOTE, null);
    }
    
    public function increasePhoto()
    {
        $this->nbPhoto++;
        $this->change('nbPhoto', ($this->nbPhoto -1), $this->nbPhoto);
    }
    
    public function decreasePhoto()
    {
        $this->nbPhoto--;
        $this->change('nbPhoto', ($this->nbPhoto +1), $this->nbPhoto);
                
    }

 

    /**
     * Add photos
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos
     */
    public function addPhotos(\Progracqteur\WikipedaleBundle\Entity\Model\Photo $photos)
    {
        //TODO: implémenter le tracking policy pour les photos
        $this->photos[] = $photos;
    }
    


    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $description = trim($description);
        if ($this->description != $description )
        {
            $this->change('description', $this->description, $description);
            $this->description = $description;
            $this->getChangeset()->addChange(ChangeService::PLACE_DETAILS, null);
        }
        
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    public function getStatusCity() 
    {
        return $this->statusCity;
    }
    
    public function getStatusBicycle()
    {
        return $this->statusBicycle;
    }
    
    public function setAccepted($accepted)
    {
        if ($this->accepted != $accepted)
        {
            $this->change('accepted', $this->accepted, $accepted);
            $this->accepted = $accepted;
            $this->getChangeset()->addChange(ChangeService::PLACE_ACCEPTED, null);
        }
    }
    
    public function isAccepted()
    {
        return $this->accepted;
    }
    
    public function setStatusBicycle($status)
    {
        if ($this->statusBicycle != $status)
        {
            $this->change('statusBicycle', $this->statusBicycle, $status);
            $this->statusBicycle = $status;
            $this->getChangeset()->addChange(ChangeService::PLACE_STATUS_BICYCLE, null);
        }
    }
    
    public function setStatusCity($status)
    {
        if ($this->statusCity != $status)
        {
            $this->change('statusCity', $this->statusCity, $status);
            $this->statusCity = $status;
            $this->getChangeset()->addChange(ChangeService::PLACE_STATUS_CITY, null);
        }
    }
    
    private function setLastUpdate(\DateTime $d)
    {
        $this->lastUpdate = $d;
    }
    
    private $proxyLastUpdate = false;
    
    private function setLastUpdateNow()
    {
        if ($this->proxyLastUpdate === false)
        {
            $this->lastUpdate = new \DateTime();
            $this->proxyLastUpdate = true;
        }
                
    }
    
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @deprecated
     * @param SerializerInterface $serializer
     * @param type $data
     * @param type $format 
     */
    public function denormalize(SerializerInterface $serializer, $data, $format = null) {
        
    }
    
    /**
     * @deprecated
     */
    public function normalize(SerializerInterface $serializer, $format = null) {
        $creator = $this->getCreator();
        return array(
            'description' => $this->getDescription(),
            'geom' => $this->getGeom()->toArrayGeoJson(),
            'id' => $this->getId(),
            'nbComm' => $this->getNbComm(),
            'nbVote' => $this->getNbVote(),
            'creator' => $creator->normalize($serializer, $format)
            //TODO: ajouter les autres paramètres
        );
        
    }

    public function getChangeset() {
        
        if ($this->changeset === null)
        {
            $this->changeset = new Place\PlaceTracking();
        }
        
        return $this->changeset;
    }

    public function addPropertyChangedListener(PropertyChangedListener $listener) {
        $this->_listeners = $listener;
    }
    

    
    
}