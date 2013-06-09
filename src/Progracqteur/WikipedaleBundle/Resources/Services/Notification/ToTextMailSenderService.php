<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Symfony\Component\Translation\Translator;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\RouterInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;

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
    
    /**
     *
     * @var Doctrine\Common\Persistence\ObjectManager 
     */
    private $om;
    
    /**
     *
     * @var Symfony\Component\Routing\RouterInterface 
     */
    private $router;
    
    private $exceptions = array();
    

    
    const DOMAIN = 'notifications';
    
    public function __construct(
            Translator $translator, 
            array $moderatorArray, 
            array $managerArray, 
            $date_format,
            ObjectManager $om,
            RouterInterface $router) {
        $this->t = $translator;
        $this->array[NotificationSubscription::KIND_MODERATOR] = $moderatorArray;
        $this->array[NotificationSubscription::KIND_MANAGER] = $managerArray;
        $this->date_format = $date_format;
        $this->om = $om;
        $this->router = $router;
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Resources\Services\Notification\SendPendingNotificationException[]
     */
    public function getExceptionsAndReset() {
        return $this->exceptions;
    }
    
    /**
     * 
     * @param Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification[] $notifications
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\User $owner
     * @return string
     */
    public function transformToText($notifications, User $owner) {
        echo "TOTEXTMAIL : écriture du mail pour l'utilisateur ".$owner->getLabel()."\n";
        
        //prepare intro text
        //
        //get notification subscription
        /*
         * array to prevent for adding a subscription twice
         */
        $previousNotificationSubscriptionIds = array();

        $notificationSubscriptions = array();

        foreach ($notifications as $notification){
            if (!in_array($notification->getSubscription()->getId(), 
                    $previousNotificationSubscriptionIds)) {
                $notificationSubscriptions[] = $notification->getSubscription();
                $previousNotificationSubscriptionIds[] = $notification->getSubscription()->getId();
            }
        }
        
        //prepare the list of notifications inside the email:
        $subText = '';
        foreach ($notificationSubscriptions as $notificationSubscription) {
            $subText .= '- ';
            
            if ($notificationSubscription->getGroup() !== null) {
                
                $subText .= $this->t->trans('mail.subscriptions.group',
                            array('%groupe%' => $notificationSubscription
                                    ->getGroup()->getName()
                        ),
                        self::DOMAIN
                        );

            } else {
                if ($notificationSubscription->getZone() !== null) {
                    
                    $subText .= $this->t->trans('mail.subscriptions.zone',
                            array('%zone%' => $notificationSubscription
                                    ->getZone()->getName()
                        ),
                        self::DOMAIN
                        );

                } else {
                    if ($notificationSubscription->getPlace() !== null) {
                        
                        $subText .= $this->t->trans('mail.subscriptions.place',
                            array('%place%' => $notificationSubscription
                                    ->getPlace()->getLabel()
                        ),
                        self::DOMAIN
                        );

                    }
                }
            }
 
        }
        
        $subText .= "\n";
        
        
        
        $t = $this->t->trans('mail.intro_text', array(
                    '%dest%' => $owner->getLabel(),
                    '%subscriptions%' => $subText
                ), self::DOMAIN);
        $t.= "\n \n";
        
        //sort by place
        
        /**
         * array to sort placetrackings by place and date
         */
        $a = array();
        
        
        
        foreach ($notifications as $notification)
        {
            $u = (int) $notification->getPlaceTracking()->getDate()->format('U');
            $a[$notification->getPlaceTracking()->getPlace()->getId()][$u] = $notification;
        }
        
        //prefix for changes items :
        $p = '- ';
        
        //create a string for each place
        foreach ($a as $placeId => $notifications_)
        {
            echo "TOTEXTMAIL : traitement de la place ".$placeId."\n";
            
            $headerShow = false;
            

            foreach($notifications_ as $timestamp => $notification) {
                try {
                
                $placetracking = $notification->getPlaceTracking();
                
                //show the header if this is the first element of the place
                if ($headerShow === false) {
                    $t .= "**".$this->t->trans('mail.place.header', 
                    array('%label%' => $placetracking->getPlace()->getLabel()), 
                        self::DOMAIN).
                        "** \n \n";

                        $headerShow = true;
                    }




                    echo "TOTEXTMAIL : traitement de la placeteracking ".$placetracking->getId()."\n";
                    $args = array(
                                    '%author%' => $placetracking->getAuthor()->getLabel(),
                                    '%label%' => $placetracking->getPlace()->getLabel(),
                                    '%date%' => $placetracking->getDate()->format($this->date_format)
                                );

                    if ($placetracking->isCreation())
                    {
                        $t .= $p.$this->t->trans('mail.place.creation', 
                                $args,
                                self::DOMAIN
                                );
                        $t .= "\n";
                        continue; //go to next event
                    }


                    $keyChanges = array();
                    foreach ($placetracking as $change)
                    {
                        $keyChanges[$change->getType()] = $change;
                    }
                    
                    //if the change is add a photo (do not consider other changes)
                    if (isset($keyChanges[ChangeService::PLACE_ADD_PHOTO]))
                    {
                        $t .= $p.$this->t->trans('mail.place.add_photo', $args, self::DOMAIN);
                        $t .= "\n";
                        continue;
                    }



                    //if the change concern the status of the place
                    if (isset($keyChanges[ChangeService::PLACE_STATUS]))
                    {
                        $status = $keyChanges[ChangeService::PLACE_STATUS]->getNewValue();
                        $args['%notation%'] = $this->om
                                ->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')
                                ->find($status->getType());

                        $t .= $p;

                        switch ($status->getValue())
                        {
                            case -1 : 
                                $t .=  $this->t->trans('mail.place.status.rejected', 
                                        $args, self::DOMAIN);
                                break;
                            case 0 :
                                $t .=  $this->t->trans('mail.place.status.notReviewed', 
                                        $args, self::DOMAIN);
                                break;
                            case 1 :
                                $t .=  $this->t->trans('mail.place.status.takenIntoAccount', 
                                        $args, self::DOMAIN);
                                break;
                            case 2 :
                                $t .=  $this->t->trans('mail.place.status.inChange', 
                                        $args, self::DOMAIN);
                                break;
                            case 3 :
                                $t .=  $this->t->trans('mail.place.status.success', 
                                        $args, self::DOMAIN);
                                break;
                        }

                        $t .= "\n";
                    }

                    //if the author added a private comment
                    if (isset($keyChanges[ChangeService::PLACE_COMMENT_MODERATOR_MANAGER_ADD])) {
                        $t .= $p. $this->t->trans('mail.place.comment.private_add',
                                $args, self::DOMAIN);
                        $t .= "\n";

                        //retrieve the comment
                        $comment = $this->om
                                ->getRepository('ProgracqteurWikipedaleBundle:Model\Comment')
                                ->find($keyChanges[ChangeService::PLACE_COMMENT_MODERATOR_MANAGER_ADD]
                                        ->getNewValue());

                        if ($comment !== null) {
                            $t .= ">".$comment->getContent();
                            $t .= "\n";
                        }


                    }


                    //if a manager was assigned
                    if (isset($keyChanges[ChangeService::PLACE_MANAGER_ADD])
                            OR isset($keyChanges[ChangeService::PLACE_MANAGER_ALTER])) {

                        if (isset($keyChanges[ChangeService::PLACE_MANAGER_ADD]))
                            $temp_ch = $keyChanges[ChangeService::PLACE_MANAGER_ADD];
                        elseif (isset($keyChanges[ChangeService::PLACE_MANAGER_ALTER])) 
                            $temp_ch = $keyChanges[ChangeService::PLACE_MANAGER_ALTER];

                        $manager = $this->om
                                ->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                                ->find($temp_ch->getNewValue());

                        //if the manager is the actual owner of the notification
                        $groups = $ns->getOwner()->getGroups();
                        $groupIds = array();

                        foreach ($groups as $group) {
                            $groupIds[] = $group->getId();
                        }



                        if (in_array($manager->getId(), $groupIds)){
                            $t.= $p. $this->t->trans('mail.place.manager.you', $args, self::DOMAIN);
                        } else {
                            $args['%manager%'] = $manager->getName();
                            $t.= $p . $this->t->trans('mail.place.manager.add', $args, self::DOMAIN);
                        }


                        unset($temp_ch);

                        $t.= "\n";
                    }

                    if (isset($keyChanges[ChangeService::PLACE_MANAGER_REMOVE])) {
                        $t.= $p . $this->t->trans('mail.place.manager.remove', $args, self::DOMAIN);
                        $t.="\n";
                    }


                    //if the changes are other : 


                    //count the changes
                    $nb = 0;
                    $key_not_to_track = array(ChangeService::PLACE_CREATION, 
                        ChangeService::PLACE_STATUS, ChangeService::PLACE_ADD_PHOTO,
                        ChangeService::PLACE_MANAGER_ADD, ChangeService::PLACE_MANAGER_ALTER,
                        ChangeService::PLACE_MANAGER_REMOVE, 
                        ChangeService::PLACE_COMMENT_MODERATOR_MANAGER_ADD);
                    $changes = array();


                    foreach($keyChanges as $key => $value) {

                        if (
                                in_array($key, $this->array[$notification
                                        ->getSubscription()
                                        ->getKind()])
                                && !in_array($key, $key_not_to_track)
                                ) {
                            $nb++;
                            $changes[] = $value;
                        } else {
                            echo "TOTEXTMAIL : change $key not selected or previously processed \n";
                        }
                    }

                    //if only one : 
                    if ($nb == 1)
                    {
                        $args['%change%'] = 
                             $this->getStringFromChangeType($changes[0]->getType());
                        $t .= $p.$this->t->trans('mail.place.change.one', $args, self::DOMAIN);
                        $t .= "\n";
                    }

                    if ($nb == 2)
                    {
                        $args['%change_%'] = 
                             $this->getStringFromChangeType($changes[0]->getType());
                        $args['%change__%'] = 
                             $this->getStringFromChangeType($changes[1]->getType());
                        $t .= $p.$this->t->trans('mail.place.change.two', $args, self::DOMAIN);
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
                        $t .=  $p.$this->t->transChoice('mail.place.change.more', $more, $args, self::DOMAIN);
                        $t .= "\n";
                    }
                } catch (\Exception $e) {
                    $exception = new SendPendingNotificationException(
                            $notification, 
                            'error during transformation to text of the placetracking',
                            0,
                            $e);
                    $this->exceptions[] = $exception;
                }
            }
            
            $t .= "\n\n";
            
            $t .= $this->addPlacePresentation($placetracking->getPlace());
            
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
                return $this->t->trans('mail.place.change.item.address' , array(), $d);
                break;
            case ChangeService::PLACE_DESCRIPTION:
                return $this->t->trans('mail.place.change.item.description', array(), $d);
                break;
            case ChangeService::PLACE_GEOM:
                return $this->t->trans('mail.place.change.item.geom', array(), $d);
                break;
            case ChangeService::PLACE_ADD_CATEGORY:
            case ChangeService::PLACE_REMOVE_CATEGORY:
                return $this->t->trans('mail.place.change.item.category', array(), $d);
                break;
            case ChangeService::PLACE_PLACETYPE_ALTER:
                return $this->t->trans('mail.place.change.item.place_type', array(), $d);
            case ChangeService::PLACE_MODERATOR_COMMENT_ALTER:
                return $this->t->trans('mail.place.change.item.moderator_comment', array(), $d);
            default:
                return $this->t->trans('mail.place.change.item.other', array(), $d);
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
        
        $t.= '>'.$place->getDescription();
        
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

            $t.= '>'.$place->getModeratorComment();

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
        
        $t.= $this->t->trans('mail.place.presentation.link', array(
            '%url%' => $this->router->generate('wikipedale_homepage', array(
                    'id' => $place->getId()
                ), true)
        ), self::DOMAIN);
        
        return $t;
        
    }
}

