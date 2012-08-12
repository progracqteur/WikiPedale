<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security;

use Symfony\Component\HttpFoundation\Session;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Description of ChangeService
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ChangeService {
    
    const PLACE_CREATION = 100;
    const PLACE_DETAILS = 105;
    const PLACE_ADD_COMMENT = 110;
    const PLACE_ADD_VOTE = 120;
    const PLACE_ADD_PHOTO = 130;
    const PLACE_STATUS_BICYCLE = 140;
    const PLACE_STATUS_CITY = 150;
    const PLACE_CREATOR = 160;
    const PLACE_ACCEPTED = 170;
    
    
    /**
     *
     * @var Symfony\Component\Security\Core\SecurityContext 
     */
    private $securityContext;
    
    public function __construct($securityContext)
    {
        $this->securityContext = $securityContext;
    }
    
    public function checkChangesAreAllowed(ChangeableInterface $object) 
    {
        /**
         * @var Progracqteur\WikipedaleBundle\Entity\Management\User
         */
        $author = $object->getChangeset()->getAuthor();
            if ($author === null)
            {
                $author = $this->securityContext->getToken()->getUser();
            }
    
        //s'il s'agit d'une création
        if ($object->getChangeset()->isCreation())
        {
            if ($object instanceof Place) {
                // si l'utilisateur est authentifié, il en peut créer un objet pour un autre
                if ( $this->securityContext->isGranted('IS_AUTHENTICATED_FULLY'))
                {
                    if ( $object->getCreator()->equals($this->securityContext->getToken()->getUser()))
                    {
                        return true;
                    } else 
                    {
                        throw ChangeException::mayNotCreateEntityForAnotherUser();
                    }
                } else { //si l'utilisateur n'est pas enregistré, alors il ne peut 
                    //pas créer d'objet pour un utilisateur enregistré
                    if ( $object->getCreator()->isRegistered() === true )
                    {
                        throw ChangeException::mayNotCreateEntityForAnotherUser();
                    } else {
                        return true;
                    }
                }
            } 
        }
        
        foreach ($object->getChangeset() as $change)
        {
            switch ($change->getType())
            {
                //pour les objets Place
                case self::PLACE_ADD_COMMENT : 
                case self::PLACE_ADD_VOTE :
                    continue; //tout le monde peut ajouter un commentaire ou un vote
                    break;
                case self::PLACE_CREATOR : 
                    throw ChangeException::param('creator');
                    break;
                case self::PLACE_STATUS_BICYCLE :
                    if ( $this->securityContext->isGranted(User::ROLE_STATUS_BICYCLE)
                            )
                    {
                        continue;
                    } else {
                        throw ChangeException::param('statusBicycle');
                    }
                    break;
                case self::PLACE_STATUS_CITY :
                    if ( $this->securityContext->isGranted(User::ROLE_STATUS_CITY)
                            )
                    {
                        continue;
                    } else {
                        throw ChangeException::param('statusCity');
                    }
                    break;
                 case self::PLACE_ACCEPTED:
                     if ($this->securityContext->isGranted(User::ROLE_ADMIN))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('accepted');
                     }
                 case self::PLACE_DETAILS :
                     //TODO implémenter le rôle particulier 'enregistré'
                     continue;
                     break;
                 case self::PLACE_ADD_PHOTO:
                     //TODO : implémenter qui peut ajouter des photos
                     continue;
                     break;
                 default:
                     throw ChangeException::param('inconnu');
            
            }
            
        }
        
        return true;
    }
    
    
}

