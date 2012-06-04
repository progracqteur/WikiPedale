<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;

/**
 * Description of PlaceController
 *
 * @author Julien Fastré
 */
class ManagerController extends Controller {
    
    public function toCityAction($city, Request $request)
    {
        $session = $this->getRequest()->getSession();
	
	switch ($city) {
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
    	}
        
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
         $session->set('city_center', null);
         
         $url = $request->get('url', null);
        
        if ($url === null)
        {
            $url = $this->generateUrl('wikipedale_homepage');
        }
        
        return $this->redirect($url);
     }
    
}

