<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\ToTextMailSenderService;
use Symfony\Component\Translation\Translator;
use Swift_Mailer;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSender;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\SendPendingNotificationException;

/**
 * Description of NotificationSender
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationMailSender extends NotificationSender {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\Notification\ToTextMailSenderService
     */
    private $toTextService;
    
    
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
            Swift_Mailer $mailer,
            Translator $translator
            ) 
    {
        $this->toTextService = $toTextService;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }
    
    
    public function addNotification(PendingNotification $notification) {
                $this->notificationToSend[$notification->getSubscription()
                        ->getOwner()->getId()][] = $notification;
    }

    public function send() 
    {
        foreach($this->notificationToSend as $ownerId => $notifications){
            $userEmail = null; 
            $owner = null;
            
            
            if (isset($notifications[0])) {
                //add user email only one time...
                if ($userEmail === null) {
                    $owner = $notifications[0]->getSubscription()->getOwner();
                    $userEmail = $owner->getEmail();
                }
                    
            }
            
            
            try {
                $text = $this->toTextService
                        ->transformToText($notifications, 
                                $owner);
            } catch (\Exception $e) {
                foreach ($notifications as $notification) {
                    $exception = new SendPendingNotificationException(
                            $notification, 
                            'Error during transformation to text', 
                            0, 
                            $e);
                    $this->postProcess($notification, $exception);
                }
                
                continue; //should pass sending this email
            }
            
            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject($this->translator->trans('mail.subject', array(), ToTextMailSenderService::DOMAIN))
                    ->setFrom('no-reply@uello.be')
                    ->setTo($userEmail)
                    ->setBody(
                        $text
                        )
                    ;
                
                $this->mailer->send($message);
                
                //postProcess sending, according to the exceptions eventually
                //stored in the toTextService
                
                $storedExceptions = $this->toTextService->getExceptionsAndReset();
                
                foreach ($notifications as $notification) {
                    
                    $problem = false;
                    
                    foreach($storedExceptions as $storedException){
                        if ($notification->getId() === $storedException
                                ->getPendingNotification()
                                ->getId()) {
                            
                            $this->postProcess($notification, $storedException);
                            $problem = true;
                            break;
                        }
                    }
                    
                    if ($problem === false)
                        $this->postProcess($notification, null);
                }
                    
            } catch (\Exception $e) {
                foreach ($notifications as $notification) {
                    $exception = new SendPendingNotificationException(
                            $notification, 
                            'Error during sending an email', 
                            0, 
                            $e);
                    $this->postProcess($notification, $exception);
                }
            }
        }
    }

    public function getKey() {
        return NotificationSubscription::TRANSPORTER_MAIL;
    }    
}

