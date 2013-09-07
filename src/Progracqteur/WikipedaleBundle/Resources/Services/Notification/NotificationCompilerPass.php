<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Description of NotificationCompilerPass
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationCompilerPass implements CompilerPassInterface {
    
    private $notificationProcessors;
    
    private $senderServices;
    
    /**
     * match notification processors with notification senders
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     */
    public function process(ContainerBuilder $container) {
        
        $cornerDefinition = $container
                ->getDefinition('progracqteur.wikipedale.notification.corner');
        
        $this->notificationProcessors = $container
                ->findTaggedServiceIds('progracqteur.wikipedale.notification.processor');
        
        if (count($this->notificationProcessors) === 0) {
            return;
        }
        
        $this->senderServices = $container
                ->findTaggedServiceIds('progracqteur.wikipedale.notification.transport');
        
        if (count($this->senderServices) === 0) {
            return;
        }
        
        foreach ($this->notificationProcessors as $processor_id => $processor_attributes) {
            
            $cornerDefinition->addMethodCall(
                    'addProcessorId', array($processor_id)
                    );
            
            foreach($this->senderServices as $sender_id => $sender_attributes){
                $processor = $container->getDefinition($processor_id);
                $processor->addMethodCall(
                        'addTransporter', 
                        array(new Reference($sender_id))
                        );
                
                //add a reference to the notification corner
                $container->getDefinition($sender_id)
                        ->addMethodCall(
                                'setNotificationCorner', array(
                                    new Reference('progracqteur.wikipedale.notification.corner')
                                    )
                                );
            }
          
        }
        
        foreach ($this->senderServices as $sender_id => $sender_attributes) {
            $cornerDefinition->addMethodCall('addTransporterId', array($sender_id));
        }
        
        
    }    
    
    public function getNotifiers(){
        return $this->notificationProcessors;
    }
    
    public function getTransporters() {
        return $this->senderServices;
    }
    
    
}

