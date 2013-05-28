<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Description of ReachableRoleService
 *
 * @author julienfastre
 */
class ReachableRoleService {
    
    private $cacheRoles;
    
    /**
     *
     * @var \Symfony\Component\Security\Core\Role\RoleHierarchyInterface 
     */
    private $roleHierarchy;
    
    public function __construct(RoleHierarchyInterface $roleHierarchy) {
        $this->roleHierarchy = $roleHierarchy;
    }
    
    
    /**
     * 
     * @param string $role
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User|\Progracqteur\WikipedaleBundle\Entity\Management\Group $userOrGroup
     * @return boolean
     * @throws \Exception if $userOrGroup is not the correct type
     */
    public function hasRole($role_searched, $userOrGroup) {
        
        if ($userOrGroup instanceof User) {
            $type = 'user';
            $id = $userOrGroup->getId();
            $roles = $userOrGroup->getRoles();
        } elseif ($userOrGroup instanceof Group) {
            $type = 'group';
            $id = $userOrGroup->getId();
            $roles = $userOrGroup->getRoles();
        } else {
            throw \Exception('you must enter a role or group, instanceof '
                    .  \get_class($userOrGroup).' given');
        }
        
        //cache the roles to avoid repeated operation on same users
        if (!isset($this->cacheRoles[$type][$id])){
            
            $rolesAvailables = array();
            
            foreach($roles as $string) {
                $rolesAvailables[] = new Role($string);
            }
            
            $roleInterfaces = $this->roleHierarchy->getReachableRoles($rolesAvailables);
            
            foreach ($roleInterfaces as $role) {
                $this->cacheRoles[$type][$id][] = $role->getRole();
            }
        }
        
        //check for the role
        if (in_array($role_searched, $this->cacheRoles[$type][$id])) {
            return true;
        } else {
            die($role_searched);
            return false;
        }
    }
    
    
}


