<?php

namespace Progracqteur\WikipedaleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

/**
 * Description of NotificationCommand
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationBisCommand extends ContainerAwareCommand {
    
    const ARGUMENT_FREQUENCY = 'frequency';
    
    public function configure() {
        $this->setName('wikipedale:notification:send2')
                ->setDescription('Send notifications of wikipedale2')
                ->addArgument(self::ARGUMENT_FREQUENCY, InputArgument::REQUIRED, "Frequency")
                ;
    }
    
    public function execute(InputInterface $input, OutputInterface $output) {
        
        
        //set the locale to FR:
        $this->getContainer()->get('translator')->setLocale('fr');
        //set the URL and scheme
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost('uello.be');
        $context->setScheme('http');
        
        $nc = $this->getContainer()->get('progracqteur.wikipedale.notification.corner');
        
        $processors = $nc->getProcessors();
        
        foreach ($processors as $processor) {
            echo "NotificationBisCommand: process on ".$processor->getKey().get_class($processor)." \n";
            echo $processor->process($input->getArgument(self::ARGUMENT_FREQUENCY));
        }
        
        $transporters = $nc->getTransporters();
        
        foreach ($transporters as $transporter) {
            $transporter->send();
        }
        
        $mailer = $this->getContainer()->get('mailer');
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->getContainer()->get('swiftmailer.transport.real');

        //$spool->flushQueue($transport);
        
        foreach($processors as $processor) {
            //$processor->finishProcess();
        }
        
        echo "ok ! \n";
        
        
    }
    
}

