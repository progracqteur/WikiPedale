<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;


/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Comment
 */
class Comment
{
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var boolean
     */
    private $published = true;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private $place;
    
    /**
     * @var string
     */
    private $type;
    
    const TYPE_MODERATOR_MANAGER = 'mmm';
    const TYPE_PUBLIC = 'public';

    
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
        $this->updateDate = new \DateTime(); //TODO
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
     * @return Comment
     */
    public function setContent($text)
    {
        $this->content = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Comment
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Comment
     */
    private function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Comment
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
    
        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set creator
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User $creator
     * @return Comment
     */
    public function setCreator(\Progracqteur\WikipedaleBundle\Entity\Management\User $creator = null)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set place
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place $place
     * @return Comment
     */
    public function setPlace(\Progracqteur\WikipedaleBundle\Entity\Model\Place $place = null)
    {
        if ($this->place !== null) {
            throw new \Exception("You cannot switch the comment place !");
        }
        
        $this->place = $place;
        return $this;
    }

    /**
     * Get place
     *
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Place 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Comment
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * called by prePersist lifeCycleEvent
     */
    public function registerToPlace() {
        $this->place->registerComment($this);
    }
}