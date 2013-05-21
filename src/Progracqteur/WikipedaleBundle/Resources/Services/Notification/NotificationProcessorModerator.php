<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationProcessor;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

/**
 * Description of NotificationProcessorModerator
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationProcessorModerator extends NotificationProcessor {
    
    private $om;
    
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }


    public function acceptTransporter() {
        return array(NotificationSubscription::TRANSPORTER_MAIL);
    }

    public function getKey() {
        
    }

    public function process($frequency) {
        foreach ($this->transporters as $t) {
            var_dump($t->getKey());
        }
    }    
}

