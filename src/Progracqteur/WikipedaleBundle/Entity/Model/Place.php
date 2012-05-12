<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;

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
     * @var hash $adress
     */
    private $adress;

    /**
     * @var point $geom
     */
    private $geom;

    /**
     * @var string $desc
     */
    private $desc;

    /**
     * @var datetime $createDate
     */
    private $createDate;

    /**
     * @var int $nbVote
     */
    private $nbVote;

    /**
     * @var int $nbComm
     */
    private $nbComm;

    /**
     * @var hash $infos
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
     * @param hash $adress
     */
    public function setAdress(\hash $adress)
    {
        $this->adress = $adress;
    }

    /**
     * Get adress
     *
     * @return hash 
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set geom
     *
     * @param point $geom
     */
    public function setGeom($geom)
    {
        $this->geom = $geom;
    }

    /**
     * Get geom
     *
     * @return point 
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
    public function setCreateDate($createDate)
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
     * Set nbVote
     *
     * @param int $nbVote
     */
    public function setNbVote(\int $nbVote)
    {
        $this->nbVote = $nbVote;
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
     * Set nbComm
     *
     * @param int $nbComm
     */
    public function setNbComm(\int $nbComm)
    {
        $this->nbComm = $nbComm;
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
     * @param hash $infos
     */
    public function setInfos(\hash $infos)
    {
        $this->infos = $infos;
    }

    /**
     * Get infos
     *
     * @return hash 
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
}