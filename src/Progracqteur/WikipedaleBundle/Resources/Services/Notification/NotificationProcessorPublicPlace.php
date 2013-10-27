<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationProcessor;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Doctrine\ORM\Mapping\ClassMetadata;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilterBySubscriptionPublicPlace;

/**
 * This class process the sending of change's subscription to individual place
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationProcessorPublicPlace extends NotificationProcessor {
    
    /**
     *
     * @var \Doctrine\Common\Persistence\ObjectManager 
     */
    private $om;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter 
     */
    private $filterByRole;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter 
     */
    private $filterBySubscription;
    
    
 
    public function __construct(
            ObjectManager $om,             
            NotificationFilterByRole $filterByRole,
            NotificationFilterBySubscriptionPublicPlace $filterBySubscriptionPublicPlace) {
        $this->filterByRole = $filterByRole;
        $this->filterBySubscription = $filterBySubscriptionPublicPlace;
        $this->om = $om;
    }

    
    public function acceptTransporter() {
        return array(NotificationSubscription::TRANSPORTER_MAIL);
    }

    public function finishProcess() {
        
    }

    public function getForm(User $user, NotificationSubscription $notification) {
        
    }

    public function getFormTemplate() {
        
    }

    public function getKey() {
        return NotificationSubscription::KIND_PUBLIC_PLACE;
    }

    public function mayBeCreated(User $user) {
        return false;
    }

    public function postSendingProcess(PendingNotification $notification, \Exception $exception = null) {
        if ($exception === null) {
            
            echo "NPPublicPlace : traitement de pendingNotification ".$notification->getId().
                    " (placetracking ".
                    $notification->getPlaceTracking()->getId().
                    ") terminé \n";
            $this->om->remove($notification);
            
        } else {
            echo "NPPublicPlace : problème pour pendingNotification ".$notification->getId()." \n";
            echo $exception->getMessage()." \n";
            echo $exception->getCode()." \n";
            echo "file : ".$exception->getFile()." line : ".$exception->getLine()."\n";
            
            if ($exception->getPrevious() !== null) {
                echo "previous exception : \n";
                $e = $exception->getPrevious();
                echo $e->getMessage()." \n";
                echo $e->getCode()." \n";
                echo "file : ".$e->getFile()." line : ".$e->getLine();
                echo "\n";
            }
            
            
            echo $exception->getTraceAsString();
            echo "\n\n\n\n\n\n\n";
        }
    }

    public function process($frequency) {
        //get pending notifications
        $pendingNotifications = $this->om->createQuery(
                'SELECT pn 
                    FROM ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification pn
                    JOIN pn.subscription s
                    WHERE s.frequency = :frequency 
                    AND s.kind like :subscription_kind'
                )
                ->setParameter('frequency', $frequency)
                ->setParameter('subscription_kind', $this->getKey())
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'subscription', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'placeTracking', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Model\Place\PlaceTracking', 'place', ClassMetadata::FETCH_EAGER)
                ->getResult();
        
        //filter notifications.
        //if the notification may NOT be sent, they are removed from pendingNotificaitons
        //and from the OM
        foreach ($pendingNotifications as $key => $notification) {
            if ($this->filterByRole->mayBeSend($notification->getPlaceTracking(), 
                    $notification->getSubscription()))
            {
                if ($this->filterBySubscription
                        ->mayBeSend($notification->getPlaceTracking(), 
                                $notification->getSubscription())) {

                    echo "NPPublicPlace: Notification de la placeTracking ". 
                            $notification->getPlaceTracking()->getId() .
                            " (placeid) ".$notification->getPlaceTracking()->getPlace()->getId().
                            " à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                            "\n";

                } else {
                    echo "NPPublicPlace: Refus DE Notification de la placeTracking par FilterBySubscriptionPublicPlace ". 
                            $notification->getPlaceTracking()->getId() .
                            " (placeid ".$notification->getPlaceTracking()->getPlace()->getId().
                            ") à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                            "\n";
                    
                    $this->om->remove($notification);
                    unset($pendingNotifications[$key]);
                }
                
             } else {
                echo "NPPublicPlace: Interdiction De Notification de la placeTracking par FilterByRole ". 
                        $notification->getPlaceTracking()->getId() .
                        " (placeid ".$notification->getPlaceTracking()->getPlace()->getId().
                        ") à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                        "\n";
                $this->om->remove($notification);
                unset($pendingNotifications[$key]);
                
            }
        }
        
        //add notification to transporter
        foreach ($pendingNotifications as $notification) {
            
            $transporter = $this->getTransporter($notification->getSubscription()->getTransporter());
            $transporter->addNotification($notification);
            
        }
        
    }    
}

