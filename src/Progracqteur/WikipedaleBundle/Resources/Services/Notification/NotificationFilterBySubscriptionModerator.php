<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;

/**
 * Description of NotificationFilterBySubscriptionKind
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationFilterBySubscriptionModerator implements NotificationFilter {
    
    private $listEvents;
    
    public function __construct($listEvents) {
        $this->listEvents = $listEvents;
    }
    
    public function mayBeSend(ChangesetInterface $changeset, NotificationSubscription $subscription) {
        
        if ($subscription->getKind() !== NotificationSubscription::KIND_MODERATOR) {
            return false;
        }
        
        if ($changeset instanceof PlaceTracking) {
            return true;
        } else {
            return false;
        }
    }    
}

