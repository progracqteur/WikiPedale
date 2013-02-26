<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 * @author Julien Fastré <julien arobase fastre point info>
 */
class DefaultController extends Controller
{
    
    
    public function aboutAction()
    {
        return $this->render('ProgracqteurWikipedaleBundle:Default:about.html.twig');
    }
    
    public function homepageAction()
    {
        $id = $this->getRequest()->get('id', null);
        
        
        
        if ($id != null)
        {
            $em = $this->getDoctrine()->getEntityManager();
            
            $place = $em->getRepository('ProgracqteurWikipedaleBundle:Model\Place')
                    ->find($id);
            
            if ($place === null OR $place->isAccepted() == FALSE)
            {
                throw $this->createNotFoundException('errors.404.place.not_found');
            }
            
            $stringGeo = $this->get('progracqteur.wikipedale.geoservice')->toString($place->getGeom());
            
            $city = $em->createQuery('select c 
                    from ProgracqteurWikipedaleBundle:Management\Zone c
                    where COVERS(c.polygon, :geom) = true and c.type = :type
                ')
                    ->setParameter('geom', $stringGeo)
                    ->setParameter('type', 'city')
                    ->getSingleResult();
            
            $this->getRequest()->getSession()->set('city', $city);
        }
        
        if ($this->getRequest()->getSession()->get('city') == null)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $cities = $em->createQuery("select c from 
                ProgracqteurWikipedaleBundle:Management\\Zone c  order by c.name")
                    
                    ->getResult();            
        } else {
            $cities = array();
        }
        
        $mainCitiesSlug = array('mons', 'liege', 'gembloux', 'namur', 'ciney', 'wanze', 'braine-l-alleud');
        $mainCities = array();
        
        foreach ($cities as $c)
        {
            if (in_array($c->getSlug(), $mainCitiesSlug))
            {
                $mainCities[] = $c;
            }
        }
        
        $categories = $this->getDoctrine()->getEntityManager()
                ->createQuery('SELECT c from 
            ProgracqteurWikipedaleBundle:Model\Category c JOIN c.parent p
            WHERE p.parent is not null
            and c.used = true
            ORDER BY p.label, c.id')
                ->getResult();
        //Todo: cachable query
        
        $placeTypes = $this->getDoctrine()->getEntityManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Model\Place\PlaceType')
                ->findAll();
        //TODO : cachable query
        
        $paramsToView = array(
                    'mainCities' => $mainCities, 
                    'cities' => $cities,
                    'categories' => $categories,
                    'placeTypes' => $placeTypes
                );

        if ($id != null)
        {
            $paramsToView['goToPlaceId'] = $id;
        }
        
        
        return $this->render('ProgracqteurWikipedaleBundle:Default:homepage.html.twig', 
                $paramsToView
            );
    }
}
