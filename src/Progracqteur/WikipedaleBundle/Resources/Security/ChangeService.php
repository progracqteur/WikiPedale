<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security;

use Symfony\Component\HttpFoundation\Session;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Doctrine\ORM\EntityManager;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Services\GeoService;
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
    const PLACE_COMMENT_ADD = 110;
    const PLACE_ADD_VOTE = 120;
    const PLACE_ADD_PHOTO = 130;
    const PLACE_REMOVE_PHOTO = 135;
    const PLACE_STATUS = 141;
    const PLACE_STATUS_BICYCLE = 140;
    const PLACE_STATUS_Zone = 150;
    const PLACE_CREATOR = 160;
    const PLACE_ACCEPTED = 170;
    const PLACE_CHECK = 173;
    const PLACE_ADD_CATEGORY = 180;
    const PLACE_REMOVE_CATEGORY = 181;
    const PLACE_MANAGER_ADD = 190;
    const PLACE_MANAGER_ALTER = 193;
    const PLACE_MANAGER_REMOVE = 198;
    const PLACE_PLACETYPE_ALTER = 200;
    const PLACE_MODERATOR_COMMENT_ALTER = 210;
    const PLACE_CREATOR_CONFIRMATION = 1600;
    
    
    /**
     *
     * @var Symfony\Component\Security\Core\SecurityContext 
     */
    private $securityContext;
    
    /**
     *
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\GeoService 
     */
    private $geoService;
    
    public function __construct($securityContext, EntityManager $em, GeoService $geoService)
    {
        $this->securityContext = $securityContext;
        $this->em = $em;
        $this->geoService = $geoService;
    }
    
    public function checkChangesAreAllowed(ChangeableInterface $object) 
    {
        /**
         * @var Progracqteur\WikipedaleBundle\Entity\Management\User
         */
        $author = $object->getChangeset()->getAuthor();
            
    
        //s'il s'agit d'une création
        if ($object->getChangeset()->isCreation() === true 
                OR
            $object->getChangeset()->isCreation() === null
                )
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
                //case self::PLACE_ADD_COMMENT : 
                case self::PLACE_CREATION:
                    continue; //tout le monde peut ajouter un emplacement
                    break;
                case self::PLACE_ADD_VOTE :
                    continue; //tout le monde peut ajouter un commentaire ou un vote
                    break;
                case self::PLACE_CREATOR : 
                    throw ChangeException::param('creator');
                    break;
                case self::PLACE_COMMENT_ADD:
                    continue; //checked by the controller CommentController
                    break;
                 case self::PLACE_ACCEPTED:
                     if ($this->securityContext->isGranted(User::ROLE_PUBLISHED))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('accepted');
                     }
                 case self::PLACE_ADDRESS : 
                     if ($this->securityContext->isGranted(User::ROLE_DETAILS_LITTLE) 
                             OR 
                             $this->securityContext->isGranted(User::ROLE_DETAILS_BIG))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('address');
                     }
                 case self::PLACE_DESCRIPTION :
                     if ($this->securityContext->isGranted(User::ROLE_DETAILS_LITTLE) 
                             OR 
                             $this->securityContext->isGranted(User::ROLE_DETAILS_BIG))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('description');
                     }
                 case self::PLACE_GEOM:
                     if ($this->securityContext->isGranted(User::ROLE_DETAILS_LITTLE) 
                             OR 
                             $this->securityContext->isGranted(User::ROLE_DETAILS_BIG))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('geom');
                     }
                 case self::PLACE_STATUS:
                     
                     
                     if (!($object instanceof Place))
                     {
                         throw new \Exception('Unexpected object : expecting Place, receiving '
                                 .get_class($object));
                     }
                     
                     $groups = $this->securityContext->getToken()->getUser()->getGroups();
                     
                     $hasRight = false;
                     
                     foreach ($groups as $group)
                     {
                         if ($group->hasRole(User::ROLE_NOTATION) 
                                 && 
                                 $group->getNotation()->getId() === $change->getNewValue()->getType())
                         {
                             
                             if ($this->geoService->covers(
                                     $group->getZone()->getPolygon(), 
                                     $object->getGeom())
                                     )
                             {
                                 $hasRight = true;
                                 break;
                             }
                         } else {
                             throw ChangeException::param('status');
                         }
                     }
                     
                     if ($hasRight == true)
                     {
                         continue;
                     }
                     
                     /**
                     $dql = "select g from ProgracqteurWikipedaleBundle:Management\Group g 
                         JOIN g.Zone c JOIN g.notation n 
                         WHERE 
                                EXISTS (select h from ProgracqteurWikipedaleBundle:Management\User u
                                           JOIN u.groups h
                                           WHERE u = :author)
                            AND
                                n.id like :notationId 
                            AND
                                COVERS(c.polygon, :geomString) = true
                            ";
                     
                     $q = $this->em->createQuery($dql);
                     $q->setParameters(array(
                         'geomString' => $geomString,
                         'author' => $author,
                         'notationId' => $change->getNewValue()->getType()
                     ));
                     
                     $groups = $q->getResult();
                     
                     if (count($groups) === 0)
                     {
                         throw ChangeException::param('Status '.$change->getnewValue()->getType().' no rights ');
                     }
                     
                     //filter by role
                     $groups_notation = array();
                     foreach ($groups as $group)
                     {
                         if ($group->hasRole('ROLE_NOTATION'))
                         {
                             $groups_notation[] = $group;
                         }
                     }
                     
                     if (count($groups_notation) > 0)
                     {
                         continue;
                     } else 
                     {
                         throw ChangeException::param('Status '.$change->getnewValue()->getType().' no rights ');
                     }*/
                     break;
                 case self::PLACE_ADD_PHOTO:
                     //l'ajout de photo est réglé dans le controleur PhotoController
                     continue;
                     break;
                 case self::PLACE_REMOVE_PHOTO:
                     //la modification des photos est réglé dans le controleur PhotoController
                     continue;
                     break;
                 case self::PLACE_ADD_CATEGORY:
                     //allowed for everybody on creation, only for group 
                     //"ROLE_CATEGORY" later
                     if ($object->getChangeset()->isCreation())
                     {
                         continue;
                     }
                     
                     if ($this->securityContext->isGranted(User::ROLE_CATEGORY))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('add category ');
                     }
                     break;
                 case self::PLACE_REMOVE_CATEGORY:
                     //allowed only for ROLE_CATEGORY
                     if ($this->securityContext->isGranted(User::ROLE_CATEGORY))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('remove category');
                     }
                     break;
                 case self::PLACE_MANAGER_ADD:
                 case self::PLACE_MANAGER_ALTER:
                 case self::PLACE_MANAGER_REMOVE:
                     if ($this->securityContext->isGranted(User::ROLE_MANAGER_ALTER))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('manager');
                     }
                     break;
                 case self::PLACE_PLACETYPE_ALTER:
                     if ($this->securityContext->isGranted(User::ROLE_PLACETYPE_ALTER))
                     {
                         continue;
                     } else {
                         throw ChangeException::param('place_type');
                     }
                     break;
                case self::PLACE_MODERATOR_COMMENT_ALTER:
                    if ($this->securityContext
                            ->isGranted(User::ROLE_MODERATOR_COMMENT_ALTER))
                    {
                        continue;
                    } else {
                        throw ChangeException::param('moderator_comment');
                    }
                    break;
                 default:
                     throw ChangeException::param('inconnu - '.$change->getType());
            
            }
            
        }
        
        return true;
    }
    
    
}

