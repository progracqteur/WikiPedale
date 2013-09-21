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
class NotificationCommand extends ContainerAwareCommand {
    
    public function configure() {
        $this->setName('wikipedale:notification:send')
                ->setDescription('Send notifications of wikipedale')
                ->addArgument('frequency', InputArgument::REQUIRED, "Frequency")
                ;
    }
    
    public function execute(InputInterface $input, OutputInterface $output) {
        echo "This command is deprecated. Use wikipedale:notication:send2 instead \n";
        exit();
        
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $pendingNotifications = $em->createQuery(
                'SELECT pn 
                    FROM ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification pn
                    JOIN pn.subscription s
                    WHERE s.frequency = :frequency'
                )
                ->setParameter('frequency', $input->getArgument('frequency'))
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'subscription', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Management\Notification\PendingNotification', 'placeTracking', ClassMetadata::FETCH_EAGER)
                ->setFetchMode('ProgracqteurWikipedaleBundle:Model\Place\PlaceTracking', 'place', ClassMetadata::FETCH_EAGER)
                ->getResult();
        
        $notifier = $this->getContainer()
                ->get('progracqteur.wikipedale.notification.sender.mail');
        
        //set the locale to FR:
        $this->getContainer()->get('translator')->setLocale('fr');
        $context = $this->getContainer()->get('router')->getContext();
        $context->setHost('uello.be');
        $context->setScheme('http');
        
        foreach ($pendingNotifications as $pn)
        {
            echo "Traitement de la pendingNotification ".$pn->getId()."\n";
            $notifier->addNotification($pn);
            $em->remove($pn);
        }
        
        $notifier->send();
        
        //send email in the spool in dev environement
        //if ($this->getContainer()->get('kernel')->getEnvironment() === 'dev') {
            $container = $this->getContainer();
            $mailer = $container->get('mailer');
            $spool = $mailer->getTransport()->getSpool();
            $transport = $container->get('swiftmailer.transport.real');

            $spool->flushQueue($transport);
        //}
        
        $em->flush();
        
        echo "ok ! \n";
        
        
    }
    
}

