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
        return 3;
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
            $admin->setEmail('admin@wikipedale.org');
            $admin->addRole(User::ROLE_ADMIN);
            $admin->setEnabled(true);
            
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
        $u->setLabel("bicycle");
        $u->setPassword("bicycle");
        $u->addGroup($g);
        
        $userManager->updateUser($u);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $str = $this->createId();
        $u->setEmail("Zone.$str@fastre.info");
        $u->setLabel("Zone $str");
        $u->setPassword("admin");
        $u->addRole(User::ROLE_STATUS_Zone);
        
        $userManager->updateUser($u);
        
        $u = $this->container->get('fos_user.user_manager')->createUser();
        $str = $this->createId();
        $u->setEmail("$str@fastre.info");
        $u->setLabel("label $str");
        $u->setPassword($str);
        
        $userManager->updateUser($u);
        $manager->flush();
        
        $this->addReference('user', $u);
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

