<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Symfony\Component\Translation\Translator;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;

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
    
    public function __construct(Translator $translator) {
        $this->t = $translator;
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
        
        //prepare common arguments for translator
        $authorLabel = $placeTracking->getAuthor()->getLabel();
        $placeName = $placeTracking->getPlace()->getLabel();
        
        $commonArgs = array(
                '%author%' => $authorLabel,
                '%place%' => $placeName
            );
        
        //domain of the messages :
        $domain = 'changes';
        
        //check if the place Tracking is a creation, return string if true
        if ($placeTracking->isCreation())
        {
            return $this->t->trans('place.is.created', $commonArgs, $domain);
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
            return $this->t->trans('place.add.photo', $commonArgs, $domain);
        }
        
        //if the changes are other : 
        
        //count the changes
        $nb = count ($changes);
        
        //if only one : 
        if ($nb == 1)
        {
            $commonArgs['%change%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            return $this->t->trans('place.change.one', $commonArgs, $domain);
        }
        
        if ($nb == 2)
        {
            $commonArgs['%change_%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            $commonArgs['%change__%'] = 
                 $this->getStringFromChangeType($changes[1]->getType());
            return $this->t->trans('place.change.two', $commonArgs, $domain);
        }
        
        if ($nb > 2)
        {
            $commonArgs['%change0%'] = 
                 $this->getStringFromChangeType($changes[0]->getType());
            $commonArgs['%change1%'] = 
                 $this->getStringFromChangeType($changes[1]->getType());
            $more = $nb - 2;
            $commonArgs['%more%'] = $more;
            return $this->t->transChoice('place.change.more', $more, $commonArgs, $domain);
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
            default:
                throw new \Exception('type inconnu dans le translator');
        }
    }
    
}

