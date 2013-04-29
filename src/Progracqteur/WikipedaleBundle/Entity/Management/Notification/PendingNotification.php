<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management\Notification;

use Doctrine\ORM\Mapping as ORM;

/**
 * PendingNotification
 */
class PendingNotification
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription
     */
    private $subscription;

    /**
     * @var \Progracqteur\WikipedaleBundle\Entity\Model\PlaceTracking
     */
    private $placeTracking;


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
     * Set subscription
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription $subscription
     * @return PendingNotification
     */
    public function setSubscription(\Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription $subscription = null)
    {
        $this->subscription = $subscription;
    
        return $this;
    }

    /**
     * Get subscription
     *
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription 
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set placeTracking
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\PlaceTracking $placeTracking
     * @return PendingNotification
     */
    public function setPlaceTracking(\Progracqteur\WikipedaleBundle\Entity\Model\PlaceTracking $placeTracking = null)
    {
        $this->placeTracking = $placeTracking;
    
        return $this;
    }

    /**
     * Get placeTracking
     *
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\PlaceTracking 
     */
    public function getPlaceTracking()
    {
        return $this->placeTracking;
    }
}