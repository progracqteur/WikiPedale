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

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Place
 */
class Place implements NormalizableInterface
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
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;
    
    private $creatorUnregisteredProxy;
    
    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Photos
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

    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $d = new \DateTime();
        $this->setLastUpdate($d);
        $this->setCreateDate($d);
        $this->infos = new Hash();
        $this->address = new Address();
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

    /**
     * Set adress
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Address $adress
     */
    public function setAddress(Address $adress)
    {
        $this->address = $adress;
        $this->setLastUpdateNow();
    }

    /**
     * Get adress
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set geom
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    public function setGeom(Point $geom)
    {
        $this->geom = $geom;
        $this->setLastUpdateNow();
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
     */
    public function setInfos(Hash $infos)
    {
        $this->infos = $infos;
        $this->setLastUpdateNow();
    }

    /**
     * Get infos
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getInfos()
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
        if ($creator instanceof UnregisteredUser)
        {
            $this->infos->creator = $creator->toHash();
            $this->creatorUnregisteredProxy = $creator;
        } else {
            $this->creator = $creator;
        }
        
        
        $this->setLastUpdateNow();
    }

    /**
     * Get creator
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        if (!is_null($this->creator))
        {
            return $this->creator;
        } 
        elseif (!is_null($this->creatorUnregisteredProxy))
        {
            return $this->creatorUnregisteredProxy;
        } 
        elseif (!is_null($this->infos->creator))
        {
            $u = UnregisteredUser::fromHash($this->infos->creator);
            $this->creatorUnregisteredProxy = $u;
            return $u;
        } 
        else 
        {
            throw new Exception('Aucun créateur enregistré');
        }
        
        
    }

    
    /**
     * Add photos
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos
     */
    public function addPhoto(\Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos)
    {
        $this->photos[] = $photos;
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
        $this->setLastUpdateNow();
    }
    
    public function increaseVote()
    {
        $this->nbVote++;
        $this->setLastUpdateNow();
    }

 

    /**
     * Add photos
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos
     */
    public function addPhotos(\Progracqteur\WikipedaleBundle\Entity\Model\Photos $photos)
    {
        $this->photos[] = $photos;
    }
    


    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        $this->setLastUpdateNow();
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
    
    public function setStatusBicycle($status)
    {
        $this->statusBicycle = $status;
    }
    
    public function setStatusCity($status)
    {
        $this->statusCity = $status;
    }
    
    private function setLastUpdate(\DateTime $d)
    {
        $this->lastUpdate = $d;
    }
    
    private function setLastUpdateNow()
    {
        $this->lastUpdate = new \DateTime();
    }
    
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    public function denormalize(SerializerInterface $serializer, $data, $format = null) {
        
    }

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
    
    
}