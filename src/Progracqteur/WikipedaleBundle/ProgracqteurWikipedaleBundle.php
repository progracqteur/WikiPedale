<?php

namespace Progracqteur\WikipedaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Progracqteur\WikipedaleBundle\Resources\Security\Authentication\WsseFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Progracqteur\WikipedaleBundle\Resources\Services\Notification\NotificationCompilerPass;

class ProgracqteurWikipedaleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
        
        $container->addCompilerPass(new NotificationCompilerPass());
    }
}
