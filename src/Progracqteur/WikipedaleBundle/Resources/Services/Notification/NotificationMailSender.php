<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\ToTextMailSenderService;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilterByRole;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilterBySubscriptionManager;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilterBySubscriptionModerator;
use Symfony\Component\Translation\Translator;
use Swift_Mailer;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

/**
 * Description of NotificationSender
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationMailSender implements NotificationSenderInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\ToTextMailSenderService
     */
    private $toTextService;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter 
     */
    private $filterByRole;
    
    private $filterBySubscription;
    
    /**
     *
     * @var Symfony\Component\Translation\Translator 
     */
    private $translator;
    
    /**
     *
     * @var Swift_Mailer 
     */
    private $mailer;
    
    private $notificationToSend = array();
    
    
    
    public function __construct(
            ToTextMailSenderService $toTextService, 
            NotificationFilterByRole $filterByRole,
            NotificationFilterBySubscriptionManager $filterBySubscriptionManager,
            NotificationFilterBySubscriptionModerator $filterBySubscriptionModerator,
            Swift_Mailer $mailer,
            Translator $translator
            ) 
    {
        $this->toTextService = $toTextService;
        $this->filterByRole = $filterByRole;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->filterBySubscription[NotificationSubscription::KIND_MANAGER] = $filterBySubscriptionManager;
        $this->filterBySubscription[NotificationSubscription::KIND_MODERATOR] = $filterBySubscriptionModerator;
    }
    
    
    public function addNotification(PendingNotification $notification) {
        if ($this->filterByRole->mayBeSend($notification->getPlaceTracking(), $notification->getSubscription()))
        {
            if ($this->filterBySubscription[$notification->getSubscription()->getKind()]
                    ->mayBeSend($notification->getPlaceTracking(), $notification->getSubscription())) {
                
                echo "Notification de la placeTracking ". 
                        $notification->getPlaceTracking()->getId() .
                        " (placeid) ".$notification->getPlaceTracking()->getPlace()->getId().
                        " à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                        "\n";
                $this->notificationToSend[$notification->getSubscription()->getOwner()->getId()]
                    [] = $notification;
                
            } else {
                echo "REFUS DE Notification de la placeTracking ". 
                        $notification->getPlaceTracking()->getId() .
                        " (placeid) ".$notification->getPlaceTracking()->getPlace()->getId().
                        " à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                        "\n";
            }
            
            
            
        } else {
            echo "INTERDICTION DE Notification de la placeTracking ". 
                    $notification->getPlaceTracking()->getId() .
                    " (placeid) ".$notification->getPlaceTracking()->getPlace()->getId().
                    " à l'utilisateur ".$notification->getSubscription()->getOwner()->getLabel().
                    "\n";
        }
    }

    public function send() 
    {
        foreach($this->notificationToSend as $ownerId => $array)
        {
            $userEmail = null; 
            $placetrackings = array();
            
            foreach($array as $notification)
            {
                $placetrackings[] = $notification->getPlaceTracking();
                
                //add user email only one time...
                if ($userEmail === null) {
                    $userEmail = $notification->getSubscription()->getOwner()->getEmail();
                }
                    
            }
            
            $text = $this->toTextService->transformToText($placetrackings, $notification->getSubscription()->getOwner(), $notification->getSubscription());
            
            $message = \Swift_Message::newInstance()
                ->setSubject($this->translator->trans('mail.subject', array(), ToTextMailSenderService::DOMAIN))
                ->setFrom('no-reply@uello.be')
                ->setTo($userEmail)
                ->setBody(
                    $text
                    )
                ;
            
            $this->mailer->send($message);
        }
    }    
}

