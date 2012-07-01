<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security;

use \Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Description of ChangeException
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ChangeException extends AccessDeniedException{
    
    
    public static function param($param)
    {
        throw new \Exception($param);
        return new self("L'utilisateur ne peut pas modifier le paramètre $param");
    }
    
    public static function mayNotCreateEntityForAnotherUser()
    {
        throw new \Exception($param);
        return new self("L'utilisateur ne peut pas créer une entité sous le nom d'un autre utilisateur");
    }
    
    
}

