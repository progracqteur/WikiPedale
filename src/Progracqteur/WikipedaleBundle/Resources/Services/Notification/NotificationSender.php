<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationCorner;
use \Exception;

/**
 *
 * @author Julien Fastré <Julien arobase fastre POINT info>
 */
abstract class NotificationSender {
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationCorner 
     */
    private $notificationCorner;
    
    public function setNotificationCorner(NotificationCorner $corner) {
        $this->notificationCorner = $corner;
    }
    
    public function postProcess(PendingNotification $notification, Exception $exception = null) {
        $this->notificationCorner
                ->getProcessor($notification->getSubscription()->getKind())
                ->postSendingProcess($notification, $exception);
    }
    
    abstract public function addNotification(PendingNotification $notification);
    
    abstract public function send();
    
    /**
     * @return string
     */
    abstract public function getKey();
    
    
}


