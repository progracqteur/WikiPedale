<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationProcessor;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Doctrine\ORM\Mapping\ClassMetadata;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of NotificationProcessorModerator
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationProcessorManager extends NotificationProcessor {
    
    private $om;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter 
     */
    private $filterByRole;
    
    private $filterBySubscription;
    
    public function __construct(
            ObjectManager $om,             
            NotificationFilterByRole $filterByRole,
            NotificationFilterBySubscriptionManager $filterBySubscriptionManager) {
        $this->filterByRole = $filterByRole;
        $this->filterBySubscription = $filterBySubscriptionManager;
        $this->om = $om;
    }


    public function acceptTransporter() {
        return array(NotificationSubscription::TRANSPORTER_MAIL);
    }

    public function getKey() {
        return NotificationSubscription::KIND_MANAGER;
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
                ->setParameter('subscription_kind', NotificationSubscription::KIND_MANAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'subscription', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'placeTracking', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Model\Place\PlaceTracking', 'place', ClassMetadata::FETCH_EAGER)
                ->getResult();
        
        
        //filter notifications
        foreach ($pendingNotifications as $key => $notification) {
            if ($this->filterByRole->mayBeSend($notification->getPlaceTracking(), $notification->getSubscription()))
            {
                if ($this->filterBySubscription
                        ->mayBeSend($notification->getPlaceTracking(), $notification->getSubscription())) {

                    echo "NPManager: Notification de la placeTracking ". 
                            $notification->getPlaceTracking()->getId() .
                            " (placeid) ".$notification->getPlaceTracking()->getPlace()->getId().
                            " à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                            "\n";

                } else {
                    echo "NPManager: Refus DE Notification de la placeTracking par FilterBySubscription ". 
                            $notification->getPlaceTracking()->getId() .
                            " (placeid ".$notification->getPlaceTracking()->getPlace()->getId().
                            ") à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                            "\n";
                    
                    $this->om->remove($notification);
                    unset($pendingNotifications[$key]);
                }



            } else {
                echo "NPManager: Interdiction De Notification de la placeTracking par FilterByRole ". 
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

    public function postSendingProcess(PendingNotification $notification, 
            \Exception $exception = null) {
        
        if ($exception === null) {
            
            echo "NPManager: traitement de pendingNotification ".$notification->getId().
                    " (placetracking ".
                    $notification->getPlaceTracking()->getId().
                    ") terminé \n";
            $this->om->remove($notification);
            
        } else {
            echo "NPManager: problème pour pendingNotification ".$notification->getId()." \n";
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
    
    public function finishProcess() {
        echo "NPManager: destruction de ".get_class($this)." \n";
        echo "NPManager: flush de l'om \n";
        $this->om->flush();
    }

    public function getForm(User $user, NotificationSubscription $notification) {
        return new Form\ProcessorManagerType($user);
    }

    public function getFormTemplate() {
        return 'ProgracqteurWikipedaleBundle:NotificationSubscriptions/Forms:manager_form.html.twig';
    }

    public function mayBeCreated(User $user) {
        return false;
    }
}

