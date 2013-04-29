<?php

namespace Progracqteur\WikipedaleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of NotificationCommand
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NotificationCommand extends ContainerAwareCommand {
    
    public function configure() {
        $this->setName('wikipedale:notification:send')
                ->setDescription('Send notifications of wikipedale')
                ->addArgument('frequency', InputArgument::REQUIRED, "Frequency")
                ;
    }
    
    public function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        
        
        echo "ok ! \n";
        
        
    }
    
}

