<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\User;


/**
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationFilterByRole {
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface $changeset
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription $subscription
     * @return boolean
     */
    public function mayBeSend(ChangesetInterface $changeset, NotificationSubscription $subscription)
    {
        if ($changeset->isCreation() === null)
        {
            echo "changeset->isCreation === null \n";
            return false;
        }
        
        if ($changeset->getPlace()->isAccepted() === false)
        {
            echo "place not accepted \n";
            return false;
        }
        
        $changes = array();
        foreach($changeset as $change)
        {
            $changes[$change->getType()] = $change;
        }
        
        //stop things private for moderator/manager
        /*if (isset($changes[ChangeService::PLACE_MODERATOR_COMMENT_ADD]))
        {
            /*if ($subscription->getOwner()->hasRole(User::ROLE_))
            {
                echo "role not correct ! \n";
                return false;
            }
        }*/
        
        return true;
        
            
    }
    
}

