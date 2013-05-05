<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;

/**
 * Description of NotificationFilterBySubscriptionKind
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationFilterBySubscriptionManager implements NotificationFilter {
    
    private $listEvents;
    
    public function __construct($listEvents) {
        $this->listEvents = $listEvents;
    }
    
    public function mayBeSend(ChangesetInterface $changeset, NotificationSubscription $subscription) {
        
        if ($subscription->getKind() !== NotificationSubscription::KIND_MANAGER) {
            return false;
        }
        
        if ($changeset instanceof PlaceTracking) {
            //check if the subscriber is the manager of the place
            $groups = $subscription->getOwner()->getGroups();
            
            $groupsManagerIds = array();
            foreach($groups as $group) {
                if ($group->getType() === Group::TYPE_MANAGER) {
                    $groupsManagerIds[] = $group->getId();
                }
            }
            
            if (in_array($changeset->getPlace()->getManager()->getId(), $groupsManagerIds)) {
                return true;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
    }    
}

