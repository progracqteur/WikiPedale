<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationSenderInterface;

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
    public function addTransporter(NotificationSenderInterface $transporter) {
        if (in_array($transporter->getKey(), $this->acceptTransporter())) {
            $this->transporters[] = $transporter;
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
    
    
}

