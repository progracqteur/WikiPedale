<?php

namespace Progracqteur\WikipedaleBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

//require Kernel
require_once __DIR__.'/../../../../../app/AppKernel.php';

/**
 * Description of PlaceControllerTest
 *
 * @author julien
 */
class PlaceControllerTest extends WebTestCase {
    
    private $_kernel;
    
    public function testValidationCorrect() 
    {
        $p = $this->getPlace();
        
        $validator = $this->getValidator();
        
        $errors = $validator->validate($p);
        
        $this->assertEquals(0, $errors->count());
        
    }
    
    public function testValidationUser()
    {
        $p = $this->getPlace(false);
        
        $validator = $this->getValidator();
        
        $errors = $validator->validate($p);
        
        $this->assertEquals(1, $errors->count());
    }
    
    public function testValidationDescriptionMoreThan10000()
    {
        $p = $this->getPlace();
        
        //ce texte contient 463 caractères
        $string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent volutpat consectetur ligula, venenatis tincidunt dui commodo et. Curabitur eleifend justo dolor. Maecenas vel ipsum sit amet odio vehicula commodo eget sit amet sem. Curabitur sagittis pulvinar mauris. Fusce ut augue vitae nulla semper malesuada eu vel massa. Suspendisse vel justo mauris. Sed mattis ipsum sed mi dapibus vestibulum. Cras vitae lorem eget tortor fringilla ornare ut vel sapien. ";
        
        $s = $string;
        for ($a = 0; $a < 10000; $a = $a+463)
        {
            $s .= $string;
        }
        
        $p->setDescription($s);
        
        
        $validator = $this->getValidator();
        
        $errors = $validator->validate($p);
        
        
        
        $this->assertEquals(1, $errors->count());
    }
    
    public function testValidationStatusBicycleOnCreation()
    {
        $p = $this->getPlace();
        
        $p->setStatusBicycle(1);
        
        
        $validator = $this->getValidator();
        
        $errors = $validator->validate($p, array('creation'));
        
        $this->assertEquals(1, $errors->count());
    }
    
    public function testValidationStatusZoneOnCreation()
    {
        $p = $this->getPlace();
        
        $p->setStatusZone(1);
        
        $validator = $this->getValidator();
        
        $errors = $validator->validate($p, array('creation'));
        
        $this->assertEquals(1, $errors->count());
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function getValidator()
    {
        if ($this->_kernel === null){
            $this->_kernel = new \AppKernel('dev', true);
            $this->_kernel->boot(); 
        }
        
        return $this->_kernel->getContainer()->get('validator');
        
        
    }
    
    
    /**
     * @return  Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private function getPlace($user = true)
    {
        $p = new Place();
        $p->setGeom($this->getRandomPoint());
        
        $p->setAddress($this->geolocate($p->getGeom()));
        
        if ($user == true)
        {
            $u = new UnregisteredUser();
            $u->setLabel('non enregistré '.$this->createId());
            $u->setEmail('test@email');
            $u->setIp('192.168.1.89');

            $p->setCreator($u);
        }
        
        
        return $p;
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
        
        //$ch = curl_init();
        
         $url = "http://open.mapquestapi.com/nominatim/v1/reverse?format=xml&lat=$lat&lon=$lon";
        
        //echo $url;
        
        //curl_setopt($ch, CURLOPT_URL, $url);
        
        //$d = curl_exec($ch);
        
        //curl_close($ch);
        
        //echo "\n";
        //echo $d;
        
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

