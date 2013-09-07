<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification;

/**
 * Description of SendPendingNotificationException
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class SendPendingNotificationException extends \Exception {
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification 
     */
    private $pendingNotification;
    
    public function __construct(PendingNotification $pendingNotifification, $message = '', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->pendingNotification = $pendingNotifification;
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Notification\PendingNotification
     */
    public function getPendingNotification() {
        return $this->pendingNotification;
    }
    
}

