<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;


/**
 * NotificationSubscription
 */
class NotificationSubscription
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $kind;

    /**
     * @var int
     */
    private $frequency;
    
    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\Zone
     */
    private $zone;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $owner;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\Group
     */
    private $group;   
    
    
        /**
     * @var string
     */
    private $transporter;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\Group
     */
    private $groupRef;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private $place;

    
    const KIND_MODERATOR = 'moderator';
    const KIND_MANAGER = 'manager';
    const KIND_PUBLIC = 'public';
    const KIND_PUBLIC_PLACE = 'publicplace';
    
    
    const FREQUENCY_MINUTELY = 60;
    const FREQUENCY_DAILY = 86400;
    const FREQUENCY_STOPPED = 0;
    
    const TRANSPORTER_MAIL = 'email';
    
    
    public function __construct() {
        $this->transporter = self::TRANSPORTER_MAIL;
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
     * Set kind
     *
     * @param string $kind
     * @return NotificationSubscription
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    
        return $this;
    }

    /**
     * Get kind
     *
     * @return string 
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set frequency
     *
     * @param \int $frequency
     * @return NotificationSubscription
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
    
        return $this;
    }

    /**
     * Get frequency
     *
     * @return \int 
     */
    public function getFrequency()
    {
        return $this->frequency;
    }

    /**
     * Add zone
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone
     * @return NotificationSubscription
     */
    public function addZone(\Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone)
    {
        $this->zone[] = $zone;
    
        return $this;
    }

    /**
     * Remove zone
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone
     */
    public function removeZone(\Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone)
    {
        $this->zone->removeElement($zone);
    }

    /**
     * Get zone
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Add owner
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User $owner
     * @return NotificationSubscription
     */
    public function addOwner(\Progracqteur\WikipedaleBundle\Entity\Management\User $owner)
    {
        $this->owner[] = $owner;
    
        return $this;
    }

    /**
     * Remove owner
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User $owner
     */
    public function removeOwner(\Progracqteur\WikipedaleBundle\Entity\Management\User $owner)
    {
        $this->owner->removeElement($owner);
    }

    /**
     * Get owner
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add group
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $group
     * @return NotificationSubscription
     */
    public function addGroup(\Progracqteur\WikipedaleBundle\Entity\Management\Group $group)
    {
        $this->group[] = $group;
    
        return $this;
    }

    /**
     * Remove group
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $group
     */
    public function removeGroup(\Progracqteur\WikipedaleBundle\Entity\Management\Group $group)
    {
        $this->group->removeElement($group);
    }

    /**
     * Get group
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * Set zone
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone
     * @return NotificationSubscription
     */
    public function setZone(\Progracqteur\WikipedaleBundle\Entity\Management\Zone $zone = null)
    {
        $this->zone = $zone;
    
        return $this;
    }

    /**
     * Set owner
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User $owner
     * @return NotificationSubscription
     */
    public function setOwner(\Progracqteur\WikipedaleBundle\Entity\Management\User $owner = null)
    {
        $this->owner = $owner;
    
        return $this;
    }

    /**
     * Set group
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $group
     * @return NotificationSubscription
     */
    public function setGroup(\Progracqteur\WikipedaleBundle\Entity\Management\Group $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Set transporter
     *
     * @param string $transporter
     * @return NotificationSubscription
     */
    public function setTransporter($transporter)
    {
        $this->transporter = $transporter;
    
        return $this;
    }

    /**
     * Get transporter
     *
     * @return string 
     */
    public function getTransporter()
    {
        return $this->transporter;
    }

    /**
     * Set groupRef
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $groupRef
     * @return NotificationSubscription
     */
    public function setGroupRef(\Progracqteur\WikipedaleBundle\Entity\Management\Group $groupRef = null)
    {
        $this->groupRef = $groupRef;
    
        return $this;
    }

    /**
     * Get groupRef
     *
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Group 
     */
    public function getGroupRef()
    {
        return $this->groupRef;
    }

    /**
     * Set place
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place $place
     * @return NotificationSubscription
     */
    public function setPlace(\Progracqteur\WikipedaleBundle\Entity\Model\Place $place = null)
    {
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
}