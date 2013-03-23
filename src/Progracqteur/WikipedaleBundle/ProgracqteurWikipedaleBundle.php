<?php

namespace Progracqteur\WikipedaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Progracqteur\WikipedaleBundle\Resources\Security\Authentication\WsseFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProgracqteurWikipedaleBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new WsseFactory());
    }
}
