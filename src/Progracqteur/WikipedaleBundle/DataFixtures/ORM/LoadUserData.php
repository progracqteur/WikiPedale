<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\Notation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $container;
    
public function getOrder() {
        return 300;
    }
    
public function load(ObjectManager $manager) {
        
        $admin = $this->container->get('fos_user.user_manager')->findUserByUsername('admin');
        $userManager = $this->container->get('fos_user.user_manager');
        
        if ($admin == null)
        {
            echo "création d'un administrateur admin \n";
            
            $admin = $this->container->get('fos_user.user_manager')->createUser();
            $admin->setUsername('admin');
            $admin->setPassword('admin');
            $admin->setEmail('admin@fastre.info');
            $admin->addRole(User::ROLE_ADMIN);
            $admin->setEnabled(true);
            $admin->setPhonenumber('+32486564');
            $admin->setLabel('Robert Delieu');
            
            $userManager->updateUser($admin);
        } else {
            echo "un utilisateur admin existe déjà \n";
        }
        
        
        
        
        $g = new Group('GRACq Mons', array('ROLE_NOTATION'));
        $g->setNotation($this->getReference('notation_cem'))
                ->setType(Group::TYPE_NOTATION);
        $city = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Zone')
                ->findOneBySlug('mons');
        $g->setZone($city);
        
        $manager->persist($g);
        
        
        
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $str = $this->createId();
        $u->setEmail("bicycle@fastre.info");
        $u->setLabel("Homme à bicyclette");
        $u->setUsername('bicycle');
        $u->setPassword("bicycle");
        $u->addGroup($g);
        $u->setPhonenumber('0123456789');
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $userManager->updateUser($u);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $str = $this->createId();
        $u->setEmail("Zone.$str@fastre.info");
        $u->setLabel("Zone $str");
        $u->setUsername('zone'.$str);
        $u->setPassword("admin");
        $u->setPhonenumber('9876543210');
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $userManager->updateUser($u);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $str = $this->createId();
        $u->setEmail("$str@fastre.info");
        $u->setLabel("label $str");
        $u->setUsername('label'.$str);
        $u->setPassword($str);
        $u->setPhonenumber('5647893210');
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $userManager->updateUser($u);
        
        
        $this->addReference('user', $u);
        
        
        $cemGroup = new Group('Conseiller en mobilité', array());
        $cemGroup->setType(Group::TYPE_MODERATOR)
                ->setNotation(
                        $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')
                            ->find('cem')
                        )
                ->setZone($city);
        
        $manager->persist($cemGroup);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $u->setEmail('cem@fastre.info');
        $u->setLabel('Monsieur Vélo Mons');
        $u->setUsername('cem');
        $u->setPassword('cem');
        $u->setPhonenumber('1256');
        
        $u->setEnabled(true);
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $u->addGroup($cemGroup);
        
        $userManager->updateUser($u);
        
        $this->addReference('cem', $u);
        $this->addReference('cemgroup', $cemGroup);
        
        $manGroup = new Group('Gestionnaire de voirie communal Mons', array());
        $manGroup->setType(Group::TYPE_MANAGER)
                ->setNotation(
                        $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')
                            ->find('cem')
                        )
                ->setZone($city);
        
        $manager->persist($manGroup);
        
        $this->addReference('manager_mons', $manGroup);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $u->setEmail('gdv@fastre.info');
        $u->setLabel('Monsieur Travaux Mons');
        $u->setUsername('gdv');
        $u->setPassword('gdv');
        $u->setPhonenumber('1256');
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $this->addReference('monsieur_velo', $u);
        
        $u->addGroup($manGroup);
        
        $userManager->updateUser($u);
        
        
        //création gestionnaire de v oirie régional
        
        $monsspw = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Zone')
                ->findOneBySlug('mons-spw');
        
        $manGroup = new Group('Gestionnaire de voirie régional', array());
        $manGroup->setType(Group::TYPE_MANAGER)
                ->setNotation(
                        $manager->getRepository('ProgracqteurWikipedaleBundle:Management\Notation')
                            ->find('cem')
                        )
                ->setZone($city);
        
        $manager->persist($manGroup);
        
        
        $this->addReference('manager_mons_spw', $manGroup);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $u->setEmail('gdvspw@fastre.info');
        $u->setLabel('Monsieur Travaux région mons');
        $u->setUsername('gdv_spw');
        $u->setPassword('gdv_spw');
        $u->setPhonenumber('1256');
        
        echo "Création de l'utilisateur ".$u->getLabel()."\n";
        
        $this->addReference('monsieur_travaux', $u);
        
        $u->addGroup($manGroup);
        
        $userManager->updateUser($u);
        
        $manager->flush();
        
    }
    
    
    //cette partie du code sert à créer des chaines de caractères aléatoires
  private $n = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

  private $z = array(3);

  public function createId() {
  
  $s = '';
  $d = array_rand($this->z);
  $dd = $this->z[$d];

   for ($i = 0; $i < $dd; $i++) {
     
     $o = array_rand($this->n);
     $s .= $this->n[$o];
   }

  return $s;
  }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}

