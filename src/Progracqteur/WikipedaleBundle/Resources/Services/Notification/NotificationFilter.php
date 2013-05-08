<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;


/**
 * 
 * an interface for filtering notification 
 * 
 * @author Julien Fastré <julien arobase fastre POINT info>
 */
interface NotificationFilter {
    
    public function mayBeSend(ChangesetInterface $changeset, NotificationSubscription $subscription);
    
}


