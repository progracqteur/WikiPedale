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
    
    public function configure() {
        $this->setName('wikipedale:notification:send2')
                ->setDescription('Send notifications of wikipedale2')
                ->addArgument('frequency', InputArgument::REQUIRED, "Frequency")
                ;
    }
    
    public function execute(InputInterface $input, OutputInterface $output) {
        
        $nm = $this->getContainer()->get('progracqteur.wikipedale.notification.processor.moderator');
        
        $nm->process(60);
        
        echo "ok ! \n";
        
        
    }
    
}

