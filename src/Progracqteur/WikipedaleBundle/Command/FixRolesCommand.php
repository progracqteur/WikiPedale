<?php

namespace Progracqteur\WikipedaleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of ImportCategories
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class FixRolesCommand extends ContainerAwareCommand {
    
    protected function configure()
    {
        $this->setName('uello:fixRoles')
                ->setDescription("Fixe les modifications des rôles");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $groups = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                ->findAll();
        
        foreach ($groups as $group) {
            if ($group->getType() === Group::TYPE_MANAGER) {
                $group->addRole(User::ROLE_MANAGER);
            }
            
            if ($group->getType() === Group::TYPE_MODERATOR) {
                $group->addRole(User::ROLE_MODERATOR);
            }
        }
        
        $manager->flush();
        
    }
    
}

