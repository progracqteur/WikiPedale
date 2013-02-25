<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;

class LoadPlaceData extends AbstractFixture implements OrderedFixtureInterface, \Symfony\Component\DependencyInjection\ContainerAwareInterface
{




    public function getOrder() {
        return 500;
    }
    
    /**
     *Cette fonction ajoute trois place à des endroits aléatoires
     * @param ObjectManager $manager 
     */
    public function load(ObjectManager $manager) {
        
        
        $notations = array('gracq', "spw", "villedemons");
        $valuesNotations = array(-1,0,1,2,3);
        
        
        for ($i=0; $i < 20; $i++)
        {
            echo "Création du point $i (associé à un utilisateur) \n";
            
            $point = $this->getRandomPoint();
        
            $str = $this->createId();

            $place = new Place();
            $place->setCreator($this->getReference('user'));
            $place->setDescription('Description '.$str);
            $place->setGeom($point);
            
            $add = $this->geolocate($point);
            
            $place->setAddress($add);
            
            //add a random category amongst the one loaded
            $cat_array = array('1', '2', '3', null, null, null, null);
            $rand = array_rand($cat_array);
            if ($cat_array[$rand] !== null) 
            {
                $cat_string_ref = 'cat'.$cat_array[$rand];
                echo "add $cat_string_ref \n";
                $place->addCategory($this->getReference('cat'.$cat_array[$rand]));
            }
            
            //ajout un statut à toutes les places, sauf à quatre d'entre elles
            if ($i != 0 OR $i != 10 OR $i != 15 OR $i != 19)
            {
                $p = new PlaceStatus();
                $p->setType($notations[array_rand($notations)])
                        ->setValue($valuesNotations[array_rand($valuesNotations)]);
                $place->addStatus($p);
                
                //ajoute un deuxième statut à une sur trois
                if ($i%3 == 0)
                {
                    $p = new PlaceStatus();
                $p->setType($notations[array_rand($notations)])
                        ->setValue($valuesNotations[array_rand($valuesNotations)]);
                $place->addStatus($p);
                }
            }
            
            //ajout d'un manager de voirie responsable à une place sur deux
            if ((($i+2) % 2) === 0)
            {
                $place->setManager($this->getReference('manager_mons'));
            }
            
            $place->getChangeset()->setAuthor($this->getReference('user'));
            
            //add a type (little 4 more frequently)
            $type_array = array('big', 'little', 'middle', 'little', 'little', 'little');
            $rand = array_rand($type_array);
            $placeType = $this->getReference('type_'.$type_array[$rand]);
            $place->setType($placeType);

            echo "type de la place est ".$place->getType()->getLabel()." \n";
            
            if (! $this->container->get('validator')->isValid($place))
            {
                throw new \Exception("place invalide");
            }
            
            $manager->persist($place);
            
            $this->addReference("PLACE_FOR_REGISTERED_USER".$i, $place);
        }
    
        //ajout de places avec utilisateurs non enregistré
        for ($i=0; $i<20; $i++)
        {
            echo "Création du point $i (utilisateur inconnu) \n";
            
            $place = new Place();
            $point = $this->getRandomPoint();
            $place->setGeom($point);

            $u = new UnregisteredUser();
            $u->setLabel('non enregistré '.$this->createId());
            $u->setEmail('test@fastre.info');
            $u->setIp('192.168.1.89');
            $u->setPhonenumber("012345678901");

            $place->setCreator($u);

            $place->setAddress($this->geolocate($point));
            
            //ajout un statut à toutes les places, sauf à quatre d'entre elles
            if ($i != 0 OR $i != 10 OR $i != 15 OR $i != 19)
            {
                $p = new PlaceStatus();
                $p->setType($notations[array_rand($notations)])
                        ->setValue($valuesNotations[array_rand($valuesNotations)]);
                $place->addStatus($p);
                
                //ajoute un deuxième statut à une sur trois
                if ($i%3 == 0)
                {
                    $p = new PlaceStatus();
                $p->setType($notations[array_rand($notations)])
                        ->setValue($valuesNotations[array_rand($valuesNotations)]);
                $place->addStatus($p);
                }
            }
            
            $place->getChangeset()->setAuthor($u);
            
            //add a random category amongst the one loaded
            $cat_array = array('1', '2', '3', null, null, null, null);
            $rand = array_rand($cat_array);
            if ($cat_array[$rand] !== null) 
            {
                $cat_string_ref = 'cat'.$cat_array[$rand];
                echo "add $cat_string_ref \n";
                $place->addCategory($this->getReference('cat'.$cat_array[$rand]));
            }
            
            //add a type (little 4 more frequently)
            $type_array = array('big', 'little', 'middle', 'little', 'little', 'little');
            $rand = array_rand($type_array);
            $placeType = $this->getReference('type_'.$type_array[$rand]);
            $place->setType($placeType);

            $manager->persist($place);
            
            $this->addReference("PLACE_FOR_UNREGISTERED_USER".$i, $place);
        }
        
        
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
  
  
  private function geolocate(Point $point)
  {
      $a = new Address();

        //si la chaine est vide, retourne le hash
        
        $dom = new \DOMDocument();
        
        $lat = $point->getLat();
        $lon = $point->getLon();
        
        $ch = curl_init();
        
         $url = "http://open.mapquestapi.com/nominatim/v1/reverse?format=xml&lat=$lat&lon=$lon";
        
        echo "Recherche de l'adresse à l'url suivante : $url \n";
        
   
        
        $dom->load($url);
        $docs = $dom->getElementsByTagName('addressparts');
        
        $doc = $docs->item(0);

        if ($dom->hasChildNodes())
        {
            foreach ($doc->childNodes as $node)
            {
                $v = $node->nodeValue;
                
                switch ($node->nodeName) {
                    case Address::CITY_DECLARATION :
                        $a->setCity($v);
                        break;
                    case Address::ADMINISTRATIVE_DECLARATION :
                        $a->setAdministrative($v);
                        break;
                    case Address::COUNTY_DECLARATION :
                        $a->setCounty($v);
                        break;
                    case Address::STATE_DISTRICT_DECLARATION :
                        $a->setStateDistrict($v);
                        break;
                    case Address::STATE_DECLARATION :
                        $a->setState($v);
                        break;
                    case Address::COUNTRY_DECLARATION :
                        $a->setCountry($v);
                        break;
                    case Address::COUNTRY_CODE_DECLARATION :
                        $a->setCountryCode($v);
                        break;
                    case Address::ROAD_DECLARATION : 
                        $a->setRoad($v);
                            break;
                    case Address::PUBLIC_BUILDING_DECLARATION :
                        $a->setPublicBuilding($v);
                        break;
                }
            }
        }
        
        return $a;
  }

    /**
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $container;
    
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
}

