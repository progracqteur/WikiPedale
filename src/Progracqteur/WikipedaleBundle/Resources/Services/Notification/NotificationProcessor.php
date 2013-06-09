<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSender;
use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

/**
 * Description of NotificationProcessor
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
abstract class NotificationProcessor {
    
    protected $transporters = array();
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface $sender
     */
    public function addTransporter(NotificationSender $transporter) {
        if (in_array($transporter->getKey(), $this->acceptTransporter())) {
            $this->transporters[] = $transporter;
        }
    }
    
    /**
     * 
     * @param string $key
     * @return \Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface
     */
    protected function getTransporter($key) {
        foreach($this->transporters as $transporter ){
            if ($transporter->getKey() === $key) {
                return $transporter;
            }
        }
    }
    
    /**
     * 
     * @param int $frequency
     */
    abstract public function process($frequency);
    
    /**
     * @return string
     */
    abstract public function getKey();
    
    /**
     * @return string[]
     */
    abstract public function acceptTransporter();
    
    abstract public function postSendingProcess(PendingNotification $notification, \Exception $exception = null);
    
    /**
     * @return bool
     */
    abstract public function mayBeCreated(User $user);
    
    /**
     * 
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    abstract public function getForm(User $user, NotificationSubscription $notification);
    
    
}

