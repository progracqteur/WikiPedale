<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\ToTextMailSenderService;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationFilter;
use Symfony\Component\Translation\Translator;
use Swift_Mailer;

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
    private $filter;
    
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
            NotificationFilter $filter,
            Swift_Mailer $mailer,
            Translator $translator
            ) 
    {
        $this->toTextService = $toTextService;
        $this->filter = $filter;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }
    
    
    public function addNotification(PendingNotification $notification) {
        if ($this->filter->mayBeSend($notification->getPlaceTracking(), $notification->getSubscription()))
        {
            $this->notificationToSend[$notification->getSubscription()->getOwner()->getId()]
                    [$notification->getPlaceTracking()->getPlace()->getId()] = $notification;
        } 
    }

    public function send() 
    {
        foreach($this->notificationToSend as $key => $array)
        {
            $userEmail = null; 
            
            foreach($array as $notification)
            {
                $placetrackings[] = $notification->getPlaceTracking();
                
                //add user email only one time...
                if ($userEmail === null) {
                    $userEmail = $notification->getSubscription()->getOwner()->getEmail();
                }
                    
            }
            
            $text = $this->toTextService->transformToText($placetrackings, $notification->getSubscription()->getOwner());
            
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

