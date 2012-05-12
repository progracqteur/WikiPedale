<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;

class LoadPlaceData extends AbstractFixture implements OrderedFixtureInterface
{




    public function getOrder() {
        return 2;
    }
    
    /**
     *Cette fonction ajoute trois place à des endroits aléatoires
     * @param ObjectManager $manager 
     */
    public function load(ObjectManager $manager) {
    
        $point = $this->getRandomPoint();
        
        $str = $this->createId();
        
        $place = new Place();
        $place->setCreator($this->getReference('user'));
        $place->setDescription('Description '.$str);
        $place->setGeom($point);
        
        
        $manager->persist($place);
        
        $point = $this->getRandomPoint();
        
        $str = $this->createId();
        
        $place = new Place();
        $place->setCreator($this->getReference('user'));
        $place->setDescription('Description '.$str);
        $place->setGeom($point);
        
        $manager->persist($place);
        
        $point = $this->getRandomPoint();
        
        $str = $this->createId();
        
        $place = new Place();
        $place->setCreator($this->getReference('user'));
        $place->setDescription('Description '.$str);
        $place->setGeom($point);
        
        //test l'ajout d'un hash pour les infos
        $h = new Hash;
        $h->test = 'test';
        $place->setInfos($h);
        
        
        $manager->persist($place);
        
        $manager->flush();
        
    
    
    
    
    
    }
    
    /**
     * La fonction renvoie un point aléatoire dans la région de Mons
     * @return \Progracqteur\WikipedaleBundle\Resources\Geo\Point 
     */
    private function getRandomPoint()
    {
        //la latitude et la longitude est défini aléatoirement, et dans la région de MOns
        $lat = rand(504500, 504570)/10000;
        $lon = rand(39400, 39620)/10000;
    
        return Point::fromLonLat($lon, $lat);
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

