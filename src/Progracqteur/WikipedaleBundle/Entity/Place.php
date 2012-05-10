<?php

namespace Progracqteur\WikipedaleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Progracqteur\WikipedaleBundle\Entity\Place
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
     * @var text $desc
     */
    private $desc;

    /**
     * @var datetime $dateIn
     */
    private $dateIn;

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
     * @param text $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * Get desc
     *
     * @return text 
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set dateIn
     *
     * @param datetime $dateIn
     */
    public function setDateIn($dateIn)
    {
        $this->dateIn = $dateIn;
    }

    /**
     * Get dateIn
     *
     * @return datetime 
     */
    public function getDateIn()
    {
        return $this->dateIn;
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
}