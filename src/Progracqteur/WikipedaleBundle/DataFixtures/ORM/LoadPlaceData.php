<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;

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
        
        for ($i=0; $i < 20; $i++)
        {
            $point = $this->getRandomPoint();
        
            $str = $this->createId();

            $place = new Place();
            $place->setCreator($this->getReference('user'));
            $place->setDescription('Description '.$str);
            $place->setGeom($point);
            
            $add = $this->geolocate($point);
            
            $place->setAddress($add);


            $manager->persist($place);
        }
    
        
        
        
        
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
  
  
  private function geolocate(Point $point)
  {
      $a = new Address();

        //si la chaine est vide, retourne le hash
        
        $dom = new \DOMDocument();
        
        $lat = $point->getLat();
        $lon = $point->getLon();
        
        $ch = curl_init();
        
         $url = "http://open.mapquestapi.com/nominatim/v1/reverse?format=xml&lat=$lat&lon=$lon";
        
        echo $url;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        $d = curl_exec($ch);
        
        curl_close($ch);
        
        echo "\n";
        echo $d;
        
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
}

