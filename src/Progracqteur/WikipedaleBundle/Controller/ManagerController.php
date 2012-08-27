<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;

/**
 * Description of PlaceController
 *
 * @author Julien Fastré
 */
class ManagerController extends Controller {
    
    public function toCityAction($citySlug, Request $request)
    {
        $session = $this->getRequest()->getSession();
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $cities = $em->createQuery("SELECT  c from ProgracqteurWikipedaleBundle:Management\\City c 
                 where c.slug = :slug")
                ->setParameter('slug', $citySlug)
                ->getResult();
        
        $city = $cities[0];
        
        if ($city === null)
        {
            throw $this->createNotFoundException("La ville demandée '$city' n'a pas été trouvée");
        }
        
        
        $session->set('city', $city);
	
	/*switch ($city) {
		case 'mons' :
			$point = Point::fromLonLat(3.971868, 50.452449);
			$session->set('city_center', $point);
                        $session->set('city', 'mons');
			break;
                case 'liege' :
                    $point = Point::fromLonLat(5.60259, 50.612973);
                    $session->set('city_center', $point);
                    $session->set('city', 'liege');
                    break;
                default:
                    throw new Exception("Le nom de la ville demandé ('$city') est inconnu dans notre système");
    	}*/
        
        $url = $request->get('url', null);
        //TODO: attention: il faut vérifier que l'URL est bien une URL du site, sinon risque de renvoyer n'importe où
        
        if ($url === null)
        {
            $url = $this->generateUrl('wikipedale_homepage');
        }
        
        return $this->redirect($url);
        
     }
     
     public function resetCityAction() {
         $session = $this->getRequest()->getSession();
         $session->set('city', null);
         
         $url = $this->getRequest()->get('url', null);
        
        if ($url === null)
        {
            $url = $this->generateUrl('wikipedale_homepage');
        }
        
        return $this->redirect($url);
     }
     
     public function wsseAuthenticateAction($_format)
     {
         if ($_format != NormalizerSerializerService::JSON_FORMAT)
         {
             throw new \Exception("Le format demandé n'est pas disponible");
         }
         
         $u = $this->get('security.context')->getToken()->getUser();
         
         $r = new \Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse();
         $r->setResults(array($u));
         
         $serializer = $this->get('progracqteurWikipedaleSerializer');
         
         $t = $serializer->serialize($r, NormalizerSerializerService::JSON_FORMAT);
         
         return new Response($t);
         
     }
    
}

