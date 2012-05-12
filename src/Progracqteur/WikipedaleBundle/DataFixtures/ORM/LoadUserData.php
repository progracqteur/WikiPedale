<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    
public function getOrder() {
        return 1;
    }
    
public function load(ObjectManager $manager) {
        $u = new User();
        $str = $this->createId();
        $u->setEmail("$str@fastre.info");
        $u->setLabel("label $str");
        $u->setPassword($str);
        
        $manager->persist($u);
        $manager->flush();
        
        $this->addReference('user', $u);
    }
    
    
    //cette partie du code sert à créer des chaines de caractères aléatoires
  private $n = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

  private $z = array(6);

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
}

