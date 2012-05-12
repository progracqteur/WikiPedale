<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Place
 */
class Place
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Hash $adress
     */
    private $adress;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    private $geom;

    /**
     * @var string $desc
     */
    private $desc = '';

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
    
    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Photos
     */
    private $photos;

    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreateDate(new \DateTime());
        $this->infos = new Hash();
        $this->adress = new Hash();
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
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Hash $adress
     */
    public function setAdress(Hash $adress)
    {
        $this->adress = $adress;
    }

    /**
     * Get adress
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set geom
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Geo\Point $geom
     */
    public function setGeom($geom)
    {
        $this->geom = $geom;
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
     * Set desc
     *
     * @param string $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->desc;
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
        $this->creator = $creator;
    }

    /**
     * Get creator
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        return $this->creator;
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
    
    public function increaseComment()
    {
        $this->nbComm++;
    }
    
    public function increaseVote()
    {
        $this->nbVote++;
    }

    /**
     * Set nbVote
     *
     * @param int $nbVote
     */
    public function setNbVote(\int $nbVote)
    {
        $this->nbVote = $nbVote;
    }

    /**
     * Set nbComm
     *
     * @param int $nbComm
     */
    public function setNbComm(\int $nbComm)
    {
        $this->nbComm = $nbComm;
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
}