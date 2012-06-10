<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;

/**
 * Ce controller est uniquement prévu pour le débogage de l'application
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class DebugController extends Controller {
    
    public function debugOneAction()
    {
        $manager = $this->getDoctrine()->getEntityManager();
        
        $u = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\User')->find(17);
        
        $point = $this->getRandomPoint();
        
            $str = $this->createId();

            $place = new Place();
            $place->setCreator($u);
            $place->setDescription('Description '.$str);
            $place->setGeom($point);
            
            $add = new Address();
            $add->setCity('mons');
            $add->setCountry('Belgium');
            $add->setCountryCode('BE');
            
            $place->setAddress($add);


            $manager->persist($place);
            $manager->flush();
           /* 
            $add = new Address();
            $add->setCity('mons');
            $add->setCountry('Belgium');
            $add->setCountryCode('BE');
            /*
            $m = Type::getTypesMap();
            
            $s = '';
            
            foreach ($m as $key => $value)
                $s .= "$key => $value \n";
        
            //Type::addType('address', '\Progracqteur\WikipedaleBundle\Resources\Doctrine\Types\AddressType' );
        
            $addrType = Type::getType('address');
            
            $platform = new PostgreSqlPlatform();
        
        
        $r = $addrType->convertToDatabaseValue($add, $platform);*/
        
        return new Response('ok');
            
            
           
            
            
            
    }
    
    public function debugTwoAction()
    {
        $manager = $this->getDoctrine()->getEntityManager();
        
        $p = $manager->getRepository('ProgracqteurWikipedaleBundle:Model\Place')->find(114);
        
        $a = $p->getAddress();
        
        return new Response($a->getCity());
            
            
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

