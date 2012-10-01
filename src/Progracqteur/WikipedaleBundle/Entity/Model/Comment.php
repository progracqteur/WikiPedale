<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Comment
 */
class Comment
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @var boolean $published
     */
    private $published;

    /**
     * @var datetime $creationDate
     */
    private $creationDate;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private $place;
    
        /**
     * @var hash $hash
     */
    private $hash;
    
    public function __construct(User $user, Place $place)
    {
        $this->setCreator($user);
        $this->setPlace($place);
        $this->hash = new Hash();
        $this->setCreationDate(new \DateTime());
    }


    /**
     * Set hash
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Hash $hash
     */
    public function setHash(Hash $hash)
    {
        $this->hash = $hash;
    }

    /**
     * Get hash
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getHash()
    {
        return $this->hash;
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
     * Set text
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set published
     *
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Set creationDate
     *
     * @param datetime $creationDate
     */
    private function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Get creationDate
     *
     * @return datetime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
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