<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;

/**
 * 
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTrackingController extends Controller {
    
    public function byCityAction($citySlug, $_format, Request $request)
    {
         $em = $this->getDoctrine()->getEntityManager();
        
        $citySlug = $this->get('progracqteur.wikipedale.slug')->slug($citySlug);
        
        if ($citySlug === null)
        {
            throw new \Exception('Renseigner une ville dans une variable \'city\' ');
        }
        
         $city = $em->getRepository('ProgracqteurWikipedaleBundle:Management\\Zone')
                ->findOneBy(array('slug' => $citySlug));
        
        if ($city === null)
        {
            throw $this->createNotFoundException("Aucune ville correspondant à $citySlug n'a pu être trouvée");
        }
        
        $max = $request->get('max', 20);
        if ($max > 100)
        {
            $response = new Response('limite du nombre de réponse dépassée. Maximum = 100');
            $response->setStatusCode(413);
            return $response;
        }
        
        $first = $request->get('first', 0);
        if ($first < 0)
        {
            $response = new Response('le paramètre first ne peut aps être négatif');
            $response->setStatusCode(400);
            return $response;
        }
        
        
        
        $tracks = $em->createQuery('SELECT p 
            from ProgracqteurWikipedaleBundle:Model\\Place\\PlaceTracking p 
               JOIN p.place pl 
            where covers(:polygon, pl.geom ) = true 
               and pl.accepted = true
            order by p.date DESC
            ')
                ->setParameter('polygon', $city->getPolygon())
                ->setFirstResult($first)
                ->setMaxResults($max)
                ->getResult();
        
        
        switch ($_format) {
            case 'json' :
                $r = new NormalizedResponse($tracks);
                $r->setLimit($max);
                $r->setStart($first);
                $normalizer = $this->get('progracqteurWikipedaleSerializer');
                $ret = $normalizer->serialize($r, $_format);
                
                return new Response($ret);
                break;
            case 'atom' :

                $r = $this->render('ProgracqteurWikipedaleBundle:History:places.atom.twig', array(
                   'title' => $city->getName(),
                   'subtitle' => "Dernières mises à jour de la ville de ".$city->getName(),
                   'tracks' => $tracks,
                   'citySlug' => $city->getSlug(),
                   'toTextService' => $this->get('progracqteur.wikipedale.place.tracking.toText'),
                   'urlFeed' => $this->generateUrl('wikipedale_history_place_by_city', 
                           array('_format' => 'atom',
                               'citySlug' => $citySlug), true)
                ));
                //$r->setCharset(Pro)
                return $r;
                break;
        }
        
        //return new Response(count($tracks).$tracks[0]->getId());
    }
    
}

