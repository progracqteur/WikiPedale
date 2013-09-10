<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Symfony\Component\DependencyInjection\Container;

/**
 * This class receive all notifications processers ans senders
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationCorner {
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\Container 
     */
    private $container;
    
    
    private $transporterIds = array();
    
    private $processorsIds = array();
    
    public function __construct(Container $container) {
        $this->container = $container;
    }

    
    public function addProcessorId($id) {
        $this->processorsIds[] = $id;
    }
    
    public function addTransporterId($id) {
        $this->transporterIds[] = $id;
    }
    
    public function getProcessors(){
        $a = array();
        
        foreach($this->processorsIds as $id) {
            $a[] = $this->container->get($id);
        }
        
        return $a;
    }
    
    public function getTransporters() {
        $a = array();
        
        foreach($this->transporterIds as $id) {
            $a[] = $this->container->get($id);
        }
        
        return $a;
    }
    
    /**
     * 
     * @param string $key
     * @return \Progracqteur\WikipedaleBundle\Resources\Services\NotificationProcessor
     */
    public function getProcessor($key) {
        foreach($this->getProcessors() as $processor) {
            if ($processor->getKey() === $key) {
                return $processor;
            }
        }
    }
    
    
    
    
    
    
}

