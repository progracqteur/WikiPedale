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
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ce controller est uniquement prévu pour le débogage de l'application
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class DebugController extends Controller {
    
    public function debugOneAction()
    {
        
        
        
        
        $manager = $this->getDoctrine()->getEntityManager();
        
        $p = $manager->getRepository('ProgracqteurWikipedaleBundle:Model\Place')->find(42);
        
        $u = $p->getCreator();
        
        if (is_null($u))
        {
            return new Response('u est null');
        }
        
        $service = $this->get('progracqteurWikipedaleSerializer');
        
        $userNormalizer = $service->getUserNormalizer();
        
        $u2 = $userNormalizer->denormalize(array('id' =>2), NormalizerSerializerService::PLACE_TYPE, 'json');

        if (is_null($u2))
        {
            return new Response('u2 est null');
        }
        
        if ($u->equals($u2))
            $t = 'true';
        else 
            $t = 'false';
        
        
        
        return new Response($t);
        
        /*$u = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\User')->find(17);
        */
        /*
        
        $place = new Place();
        $point = $this->getRandomPoint();
        $place->setGeom($point);
        
        $u = new UnregisteredUser();
        $u->setLabel('non enregistré ');
        $u->setEmail('test@email');
        $u->setIp('192.168.1.89');
        
        $place->setCreator($u);
        
        $place->setAddress($this->geolocate($point));
        
        $manager->persist($place);
        $manager->flush();
        
        
        
        
       
        
        
        
        /*$point = $this->getRandomPoint();
        
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
        /*$manager = $this->getDoctrine()->getEntityManager();
        
        $u = $manager->getRepository('ProgracqteurWikipedaleBundle:Management\User')->find(14);
        /*
        $p = $manager->getRepository('ProgracqteurWikipedaleBundle:Model\Place')->find(114);
        
        $a = $p->getAddress();
        
        return new Response($a->getCity());*/
        /*
        
         $point = $this->getRandomPoint();
        
            $str = $this->createId();

            $place = new Place();
            $place->setCreator($u);
            $place->setDescription('Description '.$str);
            $place->setGeom($point);
        
        $add = $this->geolocate($point);
        
        $place->setAddress($add);
        
        //$r = serialize($add->toArray());
        
        //Type::addType('address', '\Progracqteur\WikipedaleBundle\Resources\Doctrine\Types\AddressType' );
        
            $addrType = Type::getType('address');
            
            $platform = new PostgreSqlPlatform();
        
        $a = $add->toArray();
        
        $r = '';
        
        /*foreach ($a as $key => $value)
        {
            $r .= "$key => $value \n";
        }*/
        
        //$r .= $addrType->convertToDatabaseValue($add, $platform);
        /*
        $manager->persist($place);
        $manager->flush();
        
        
        
        
        
        
        
        return new Response('<html><head><title>debug</title></head><body>'.$r.'</body></html>');*/
            
            
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
  
  public function devSendPlaceAction(Request $request)
  {
      
      $id = $request->get('id', null);
      
      if ($id === null)
      {
      $point = $this->getRandomPoint();
        
        $str = $this->createId();
        
        $u = new UnregisteredUser();
        $u->setLabel('non enregistré ');
        $u->setEmail('test@email');
        $u->setIp('192.168.1.89');

        $place = new Place();
        $place->setCreator($u);
        $place->setDescription('Description '.$str);
        $place->setGeom($point);

        $add = $this->geolocate($point);

        $place->setAddress($add);
        }
        else {
            
            $orm = $this->getDoctrine()->getEntityManager();
            
            $place = $orm->getRepository('ProgracqteurWikipedaleBundle:Model\Place')
                    ->find($id);
            
            if ($place === null)
            {
                return $this->createNotFoundException("place avec id $id non trouvée");
                
            }
            
            
      
        }
        
        /**
         * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
         */
        $normalizer = $this->get('progracqteurWikipedaleSerializer');
        
        $placeNormalizer = $normalizer->getPlaceNormalizer();
        $a = $placeNormalizer->normalize($place);
        $ret = json_encode($a);
        
        
        return $this->render("ProgracqteurWikipedaleBundle:Dev:send_request.html.twig", array(
            'action' => $this->generateUrl('wikipedale_place_change', array('_format' => 'json')),
            'json' => $ret
        ));
        
  }
  
  public function devSendRequestWsseAction()
  {
      return $this->render("ProgracqteurWikipedaleBundle:Dev:send_request_wsse.html.twig");
  }
  
  public function devSendTestTokenAction()
  {
      return $this->render("ProgracqteurWikipedaleBundle:Dev:send_token_form.html.twig");
  }
    
    
}

