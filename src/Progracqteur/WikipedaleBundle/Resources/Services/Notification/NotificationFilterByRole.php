<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\Role;


/**
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationFilterByRole {
    
    
    private $roleHierarchy;
    
    public function __construct(RoleHierarchyInterface $roleHierarchy) {
        $this->roleHierarchy = $roleHierarchy;
    }
    
    
    private $cacheRoles = array();
    
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
            echo "FILTER BY ROLE : changeset->isCreation === null \n";
            return false;
        }
        
        if ($changeset->getPlace()->isAccepted() === false)
        {
            echo "FILTER BY ROLE place not accepted \n";
            return false;
        } 
        
        //cache the roles to avoid repeated operation on same users
        if (!isset($this->cacheRoles[$subscription->getOwner()->getId()])){
            
            $rolesAvailables = array();
            
            foreach($subscription->getOwner()->getRoles() as $string) {
                $rolesAvailables[] = new Role($string);
            }
            
            $roleInterfaces = $this->roleHierarchy->getReachableRoles($rolesAvailables);
            
            foreach ($roleInterfaces as $role) {
                $this->cacheRoles[$subscription->getOwner()->getId()][] = $role->getRole();
            }
        }
        
        foreach($changeset as $change)
        {
            if ($change->getType() === ChangeService::PLACE_COMMENT_MODERATOR_MANAGER_ADD) {
                if (!in_array(User::ROLE_COMMENT_MODERATOR_MANAGER, 
                        $this->cacheRoles[$subscription->getOwner()->getId()])
                        ){
                    echo "FILTER BY ROLE ".$subscription->getOwner()->getLabel()." does not have role ".User::ROLE_COMMENT_MODERATOR_MANAGER." \n";
                    return false;
                }
            }
        }
        

        
        return true;
        
            
    }
    
}

