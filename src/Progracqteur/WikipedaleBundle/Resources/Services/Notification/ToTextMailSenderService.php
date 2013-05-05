<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Symfony\Component\Translation\Translator;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;

/**
 * A service which transform a Notification to a text
 * This service is adapted for email.
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ToTextMailSenderService {
    
    /**
     * the translator, stored by the constructor
     * @var Symfony\Component\Translation\Translator 
     */
    private $t;
    
    private $array;
    
    private $date_format;
    

    
    const DOMAIN = 'notifications';
    
    public function __construct(Translator $translator, array $moderatorArray, array $managerArray, $date_format) {
        $this->t = $translator;
        $this->array[NotificationSubscription::KIND_MODERATOR] = $moderatorArray;
        $this->array[NotificationSubscription::KIND_MANAGER] = $managerArray;
        $this->date_format = $date_format;
    }    
    
    
    public function transformToText($placetrackings, User $owner, NotificationSubscription $ns) {
        
        //prepare text
        $t = $this->t->trans('mail.intro_text', array(
                    '%dest%' => $owner->getLabel(),
                    "%zone%" => $ns->getZone()
                ), self::DOMAIN);
        $t.= "\n \n";
        
        //sort by place
        $a = array();
        
        
        foreach ($placetrackings as $placetracking)
        {
            $u = (int) $placetracking->getDate()->format('U');
            $a[$placetracking->getPlace()->getId()][$u] = $placetracking;
        }
        
        //create a string for each place
        foreach ($a as $array)
        {

            //sort by date
            sort($array, SORT_NUMERIC);

            $t .= "**".$this->t->trans('mail.place.header', 
                    array('%label%' => $array[0]->getPlace()->getLabel()), 
                    self::DOMAIN).
                    "** \n \n";

            foreach($array as $placetracking)
            {
                $args = array(
                                '%author%' => $placetracking->getAuthor()->getLabel(),
                                '%label%' => $placetracking->getPlace()->getLabel(),
                                '%date%' => $placetracking->getDate()->format($this->date_format)
                            );

                if ($placetracking->isCreation())
                {
                    $t .= $this->t->trans('mail.place.creation', 
                            $args,
                            self::DOMAIN
                            );
                    $t .= "\n";
                    continue; //go to next event
                }

                foreach ($placetracking as $change)
                {
                    $keyChanges[$change->getType()] = $change;
                }

                //if the change is add a photo (do not consider other changes)
                if (isset($keyChanges[ChangeService::PLACE_ADD_PHOTO]))
                {
                    $t .= $this->t->trans('mail.place.add_photo', $args, self::DOMAIN);
                    $t .= "\n";
                    continue;
                }



                //if the change concern the status of the place
                if (isset($keyChanges[ChangeService::PLACE_STATUS]))
                {
                    $status = $keyChanges[ChangeService::PLACE_STATUS]->getNewValue();
                    $args['%notation%'] = $status->getType();

                    switch ($status->getValue())
                    {
                        case -1 : 
                            $t .=  $this->t->trans('mail.place.status.rejected', $args, self::DOMAIN);
                            break;
                        case 0 :
                            $t .=  $this->t->trans('mail.place.status.notReviewed', $args, self::DOMAIN);
                            break;
                        case 1 :
                            $t .=  $this->t->trans('mail.place.status.takenIntoAccount', $args, self::DOMAIN);
                            break;
                        case 2 :
                            $t .=  $this->t->trans('mail.place.status.inChange', $args, self::DOMAIN);
                            break;
                        case 3 :
                            $t .=  $this->t->trans('mail.place.status.success', $args, self::DOMAIN);
                            break;
                    }

                    $t .= "\n";
                    continue;
                }

                //if the changes are other : 

                //count the changes
                $nb = count ($changes);

                //if only one : 
                if ($nb == 1)
                {
                    $args['%change%'] = 
                         $this->getStringFromChangeType($changes[0]->getType());
                    $t .= $this->t->trans('mail.place.change.one', $args, self::DOMAIN);
                    $t .= "\n";
                }

                if ($nb == 2)
                {
                    $args['%change_%'] = 
                         $this->getStringFromChangeType($changes[0]->getType());
                    $args['%change__%'] = 
                         $this->getStringFromChangeType($changes[1]->getType());
                    $t .= $this->t->trans('mail.place.change.two', $args, self::DOMAIN);
                    $t .= "\n";
                }

                if ($nb > 2)
                {
                    $args['%change_%'] = 
                         $this->getStringFromChangeType($changes[0]->getType());
                    $args['%change__%'] = 
                         $this->getStringFromChangeType($changes[1]->getType());
                    $more = $nb - 2;
                    $args['%more%'] = $more;
                    $t .=  $this->t->transChoice('mail.place.change.more', $more, $args, self::DOMAIN);
                    $t .= "\n";
                }
            }
            
            $t .= "\n\n";
            
            $t .= $this->addPlacePresentation($array[0]->getPlace());
            
            $t .= "\n\n\n\n\n\n\n\n";
        }
        
        $t .= $this->t->trans('mail.footer_text', array(), self::DOMAIN);
        
        return $t;
          
    }
    
    private function getStringFromChangeType($type)
    {
        //domain of the translations
        $d = self::DOMAIN;
        
        switch ($type)
        {
            case ChangeService::PLACE_ADDRESS :
                return $this->t->trans('mail.change.place.address' , array(), $d);
                break;
            case ChangeService::PLACE_DESCRIPTION:
                return $this->t->trans('mail.change.place.description', array(), $d);
                break;
            case ChangeService::PLACE_GEOM:
                return $this->t->trans('mail.change.place.geom', array(), $d);
                break;
            case ChangeService::PLACE_ADD_CATEGORY:
            case ChangeService::PLACE_REMOVE_CATEGORY:
                return $this->t->trans('mail.place.change.place.category', array(), $d);
                break;
            case ChangeService::PLACE_PLACETYPE_ALTER:
                return $this->t->trans('mail.place.change.place.place_type', array(), $d);
            case ChangeService::PLACE_MODERATOR_COMMENT_ALTER:
                return $this->t->trans('mail.place.change.place.moderator_comment', array(), $d);
            default:
                return $this->t->trans('mail.place.change.place.other', array(), $d);
        }
    }
    
    
    private function addPlacePresentation(Place $place) {
        $t = '';
        
        $t.= $this->t->trans('mail.place.presentation.actual',
                array(
                    '%label%' => $place->getLabel(),
                    '%id%' => $place->getId()
                ), self::DOMAIN);
        
        $t.="\n\n";
        
        $t.= $this->t->trans('mail.place.presentation.description_header', array(), self::DOMAIN);
        
        $t.="\n";
        
        $t.= $place->getDescription();
        
        $t.="\n";
        
        $t.= $this->t->trans('mail.place.presentation.introduced_by_when', 
                array(
                    '%creator%' => $place->getCreator()->getLabel(),
                    '%create_date%' => $place->getCreateDate()->format($this->date_format)
                ), self::DOMAIN);
        
        $t.="\n";
        
        if ($place->getModeratorComment() != '') {
            $t.= $this->t->trans('mail.place.presentation.moderator_comment_header',
                    array(

                    ), self::DOMAIN);

            $t.= "\n";

            $t.= $place->getModeratorComment();

            $t.= "\n";
        }
        
        //anonymous function to show categories label
        $func = function() use ($place) {
                    $string = '';
                    $i = 0;
                    foreach ($place->getCategory() as $cat) {
                        if ($i !== 0) {
                            $string .= ', ';
                        }
                        $string.= "'".$cat->getLabel()."'";
                        
                        $i++;
                    }
                    
                    return $string;
                };
        
        $t.= $this->t->trans('mail.place.presentation.categories', array(
                '%categories%' => $func()
            ), self::DOMAIN);
        
        $t.="\n";
        
        if ($place->getManager() === null) {
            $managerLabel = $this->t->trans(
                    'mail.place.presentation.no_manager', 
                    array(),
                    self::DOMAIN
                    );
        } else {
            $managerLabel = $place->getManager()->getName();
        }
        
        $t.= $this->t->trans('mail.place.presentation.manager', array(
            '%manager_label%' => $managerLabel
        ), self::DOMAIN);
                
        $t.="\n";
        
        return $t;
        
    }
}

