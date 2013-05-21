<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;

/**
 *
 * @author Julien Fastré <Julien arobase fastre POINT info>
 */
interface NotificationSenderInterface {
    
    public function addNotification(PendingNotification $notification);
    
    public function send();
    
    /**
     * @return string
     */
    public function getKey();
    
    
}


