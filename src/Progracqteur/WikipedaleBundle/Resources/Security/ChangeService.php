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
    /**
     * @deprecated since 2012-09-27
     */
    const PLACE_DETAILS = 105;
    const PLACE_DESCRIPTION = 1050;
    const PLACE_ADDRESS = 1060;
    const PLACE_GEOM = 1070;
    const PLACE_ADD_COMMENT = 110;
    const PLACE_ADD_VOTE = 120;
    const PLACE_ADD_PHOTO = 130;
    const PLACE_REMOVE_PHOTO = 135;
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
            
    
        //s'il s'agit d'une création
        if ($object->getChangeset()->isCreation())
        {
            if ($object instanceof Place) {
                // si l'utilisateur est authentifié, il ne peut créer un objet pour un autre
                if ( $author->isRegistered())
                {
                    if ( $object->getCreator()->equals($author))
                    {
                        return true;
                    } else 
                    {
                        throw ChangeException::mayNotCreateEntityForAnotherUser(10);
                    }
                } else { //si l'utilisateur n'est pas enregistré, alors il ne peut 
                    //pas créer d'objet pour un utilisateur enregistré
                    if ( $object->getCreator()->isRegistered() === true )
                    {
                        throw ChangeException::mayNotCreateEntityForAnotherUser(100);
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
                 case self::PLACE_ADDRESS : 
                 case self::PLACE_DESCRIPTION :
                 case self::PLACE_GEOM:
                     if (! $author->isRegistered())
                     {
                         throw ChangeException::param('details');
                     }
                     continue;
                     break;
                 case self::PLACE_ADD_PHOTO:
                     //l'ajout de photo est réglé dans le controleur PhotoController
                     continue;
                     break;
                 case self::PLACE_REMOVE_PHOTO:
                     //la modification des photos est réglé dans le controleur PhotoController
                     continue;
                     break;
                 default:
                     throw ChangeException::param('inconnu');
            
            }
            
        }
        
        return true;
    }
    
    
}

