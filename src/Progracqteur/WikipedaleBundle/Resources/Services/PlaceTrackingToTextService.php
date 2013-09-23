<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Symfony\Component\Translation\Translator;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Doctrine\ORM\EntityManager;

/**
 * This class transform a placeTracking entity into a text readable by
 * an human. 
 * 
 * The texts were discussed there : https://github.com/progracqteur/WikiPedale/issues/27
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTrackingToTextService {
    
    /**
     * the translator, stored by the constructor
     * @var Symfony\Component\Translation\Translator 
     */
    private $t;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager; 
     */
    private $em;
    
    
    
    public function __construct(Translator $translator, EntityManager $em) {
        $this->t = $translator;
        $this->em = $em;
    }
    
    /**
     * Return a string, which may be displayed to the user 
     * and described the changes.
     * The string is translatable.
     * 
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking $placeTracking
     * @return string
     */
    public function toText(PlaceTracking $placeTracking)
    {
        //domain of the messages :
        $domain = 'changes';
        
        //prepare common arguments for translator
        //try {
            $authorLabel = $placeTracking->getAuthor()->getLabel();
            //FIXME: this should not throw an error !
        //}
        //catch (\Exception $e) {$authorLabel = $this->t->trans('user.unknow', array(), $domain); }
        
        $placeName = $placeTracking->getPlace()->getLabel();
        
        $args = array(
                '%author%' => $authorLabel,
                '%place%' => $placeName
            );
        
        
        //check if the place Tracking is a creation, return string if true
        if ($placeTracking->isCreation())
        {
            return $this->t->trans('place.is.created', $args, $domain);
        }
        
              
        //order the thanges to be founded by isset() function and array[key]
        $changes = $placeTracking->getChanges();
        $keyChanges = array(); 
        foreach ($changes as $change)
        {
            $keyChanges[$change->getType()] = $change;
        }
        
        //if the change is add a photo (do not consider other changes)
        if (isset($keyChanges[ChangeService::PLACE_ADD_PHOTO]))
        {
            return $this->t->trans('place.add.photo', $args, $domain);
        }
        
        //if the change concern the status of the place
        if (isset($keyChanges[ChangeService::PLACE_STATUS]))
        {
            $status = $keyChanges[ChangeService::PLACE_STATUS]->getNewValue();
            $args['%notation%'] = $status->getType();
            
            switch ($status->getValue())
            {
                case -1 : 
                    return $this->t->trans('place.status.rejected', $args, $domain);
                    break;
                case 0 :
                    return $this->t->trans('place.status.notReviewed', $args, $domain);
                    break;
                case 1 :
                    return $this->t->trans('place.status.takenIntoAccount', $args, $domain);
                    break;
                case 2 :
                    return $this->t->trans('place.status.inChange', $args, $domain);
                    break;
                case 3 :
                    return $this->t->trans('place.status.success', $args, $domain);
                    break;
            }
        }
        
        if (isset($keyChanges[ChangeService::PLACE_MANAGER_ADD]) 
                OR isset($keyChanges[ChangeService::PLACE_MANAGER_ALTER])) {
            
            if (isset($keyChanges[ChangeService::PLACE_MANAGER_ADD])) {
                $idGroupManager = $keyChanges[ChangeService::PLACE_MANAGER_ADD]->getNewValue();
            } else {
                $idGroupManager = $keyChanges[ChangeService::PLACE_MANAGER_ALTER]->getNewValue();
            }
            
            $groupManager = $this->em->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                    ->find($idGroupManager);
            
            $args['%group%'] = $groupManager->getName();
            
            return $this->t->trans('place.manager.new', $args, $domain);
            
        }
        
        //if the changes are other : 
        
        //count the changes
        $nb = count ($changes);
        
        //if only one : 
        if ($nb == 1)
        {
            $args['%change%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            return $this->t->trans('place.change.one', $args, $domain);
        }
        
        if ($nb == 2)
        {
            $args['%change_%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            $args['%change__%'] = 
                 $this->getStringFromChangeType($changes[1]->getType());
            return $this->t->trans('place.change.two', $args, $domain);
        }
        
        if ($nb > 2)
        {
            $args['%change0%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            $args['%change1%'] = 
                 $this->getStringFromChangeType($changes[1]->getType());
            $more = $nb - 2;
            $args['%more%'] = $more;
            return $this->t->transChoice('place.change.more', $more, $args, $domain);
        }
        
        
        
        
    }
    
    
    private function getStringFromChangeType($type)
    {
        //domain of the translations
        $d = 'changes';
        
        switch ($type)
        {
            case ChangeService::PLACE_ADDRESS :
                return $this->t->trans('change.place.address' , array(), $d);
                break;
            case ChangeService::PLACE_DESCRIPTION:
                return $this->t->trans('change.place.description', array(), $d);
                break;
            case ChangeService::PLACE_GEOM:
                return $this->t->trans('change.place.geom', array(), $d);
                break;
            case ChangeService::PLACE_ADD_CATEGORY:
            case ChangeService::PLACE_REMOVE_CATEGORY:
                return $this->t->trans('change.place.category', array(), $d);
                break;
            case ChangeService::PLACE_PLACETYPE_ALTER:
                return $this->t->trans('change.place.place_type', array(), $d);
            case ChangeService::PLACE_MODERATOR_COMMENT_ALTER:
                return $this->t->trans('change.place.moderator_comment', array(), $d);
            default:
                return $this->t->trans('change.place.other', array(), $d);
        }
    }
    
}

