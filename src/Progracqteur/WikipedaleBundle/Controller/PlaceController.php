<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;

/**
 * Description of PlaceController
 *
 * @author Julien Fastré
 */
class PlaceController extends Controller {
    
    
    public function viewAction($_format, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $place = $em->getRepository('ProgracqteurWikipedaleBundle:Model\\Place')->find($id);
        
        if ($place === null)
        {
            throw $this->createNotFoundException("L'endroit n'a pas été trouvé dans la base de donnée");
        }
        
        switch ($_format){
            case 'json':
                $jsonencoder = new JsonEncoder();
                $serializer = new Serializer(array(new CustomNormalizer()) , array('json' => $jsonencoder));
                $ret = $serializer->serialize($place, $_format);
                return new Response($ret);
                break;
            
            case 'html' :
                return $this->render('ProgracqteurWikipedaleBundle:Place:view.html.twig', 
                        array(
                            'place' => $place
                        ));
                break;
        }
    }
}

