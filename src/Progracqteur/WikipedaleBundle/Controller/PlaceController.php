<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Geo\BBox;
use Symfony\Component\HttpFoundation\Request;

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
		$rep = array('results' => array($place));
                $ret = $serializer->serialize($rep, $_format);
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
    
    public function listByBBoxAction($_format, Request $request){
        
        /*TODO la requête actuelle crée un rectangle grossier autour 
         * des limites de la commune pour extraire les place à l'intérieur de cette limite
         * 
         * La finesse de cette requete pourra être améliorée lorsque les coordonnées géographiques
         * des limites de communes auront été ajoutées au système
         * 
         * 
         */
        
        /* TODO
         * Pour l'instant, seuls Mons et Liège sont disponible dans cette requete. Ajouter 
         * d'autres villes si nécessaire.
         */
        $em = $this->getDoctrine()->getEntityManager();
        
        $BboxStr = $request->get('bbox', null);
        if ($BboxStr === null) {
            throw new \Exception('Fournissez un bbox');
        }
        
        $BboxArr = explode(',', $BboxStr, 4);
        
        foreach($BboxArr as $value){
            if (!is_numeric($value))
            {
                throw new \Exception("Le Bbox n'est pas valide : $BboxStr");
            }
        }
        
        
        
        
        $bbox = BBox::fromCoord($BboxArr[0], $BboxArr[1], $BboxArr[2], $BboxArr[3]);
        
        $p = $em->createQuery('SELECT p from ProgracqteurWikipedaleBundle:Model\\Place p where covers(:bbox, p.geom) = true')
                ->setParameter('bbox', $bbox->toWKT());
        
        $r = $p->getResult();
        
        switch($_format) {
            case 'json':
                $jsonencoder = new JsonEncoder();
                $serializer = new Serializer(array(new CustomNormalizer()) , array('json' => $jsonencoder));
		$rep = array('results' => $r);
                $ret = $serializer->serialize($rep, $_format);
                return new Response($ret);
                break;
            case 'html':
                return new Response('Pas encore implémenté');
                
        }
        
        
    }
    
    public function listByCityAction($_format, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $city = $request->get('city', null);
        if ($city === null)
        {
            throw new \Exception('Renseigner une ville dans une variable \'city\' ');
        }
        
        
        switch($city) {
            case 'mons':
                $bbox = BBOX::fromCoord(50.515173, 4.086857, 50.374591, 3.852024);
                break;
            case 'liege':
                $bbox = BBOX:: fromCoord(50.6910, 5.678158, 50.559469, 5.520229);
                break;
            default:
                throw new \Exception("La ville renseignée ('$city') n'est pas connue du système");
        }
        
        $p = $em->createQuery('SELECT p from ProgracqteurWikipedaleBundle:Model\\Place p where covers(:bbox, p.geom) = true')
                ->setParameter('bbox', $bbox->toWKT());
        
        $r = $p->getResult();
        
        switch($_format) {
            case 'json':
                $jsonencoder = new JsonEncoder();
                $serializer = new Serializer(array(new CustomNormalizer()) , array('json' => $jsonencoder));
		$rep = array('results' => $r);
                $ret = $serializer->serialize($rep, $_format);
                return new Response($ret);
                break;
            case 'html':
                return new Response('Pas encore implémenté');
                
        }
        
        
    }
}

