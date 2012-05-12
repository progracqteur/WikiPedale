<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Photo
 */
class Photo
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var blob $file
     */
    private $file;

    /**
     * @var datetime $createDate
     */
    private $createDate;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private $place;


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
     * Set file
     *
     * @param blob $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Get file
     *
     * @return blob 
     */
    public function getFile()
    {
        return $this->file;
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
     * Set place
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Place $place
     */
    public function setPlace(\Progracqteur\WikipedaleBundle\Entity\Model\Place $place)
    {
        $this->place = $place;
    }

    /**
     * Get place
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Place 
     */
    public function getPlace()
    {
        return $this->place;
    }
}