<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;

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
            $em = $this->getDoctrine()->getManager();
            
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
        
        /*if ($this->getRequest()->getSession()->get('city') == null)
        {*/
            $em = $this->getDoctrine()->getManager();

            $cities = $em->createQuery("select c from 
                ProgracqteurWikipedaleBundle:Management\\Zone c  order by c.name")
                    
                    ->getResult();            
        /*} else {
            $cities = array();
        }*/
        
        $mainCitiesSlug = $this->get('service_container')->getParameter('cities_in_front_page'); 
        $mainCities = array();
        
        foreach ($cities as $c)
        {
            if (in_array($c->getSlug(), $mainCitiesSlug))
            {
                $mainCities[] = $c;
            }
        }
        
        //retrieve categories depending on user's right
        
        $terms_allowed = ' ';
        $terms_allowed_array = array();
        $iTerm = 0;
        foreach ($this->get('service_container')->getParameter('place_types') 
                as $target => $array) {
            //TODO extendds to other transports
                    if ($target === 'bike') {
                        foreach ($array["terms"] as $term) {
                            if ($this->get('security.context')->isGranted(
                                    $term['mayAddToPlace'])){
                                if ($iTerm > 0) {
                                    $terms_allowed .= ', ';
                                }
                                $terms_allowed .= "'".$term['key']."'";
                                $terms_allowed_array[] = $term['key'];
                                $iTerm ++;
                            }
                            
                        }
                    }
            
                }
                
        $terms_allowed .= ' ';
        
        
        $q = sprintf('SELECT c from 
            ProgracqteurWikipedaleBundle:Model\Category c 
            WHERE  c.used = true AND c.parent is null AND c.term IN (%s)
            ORDER BY c.order, c.label', $terms_allowed);
        
        
        
        $categories = $this->getDoctrine()->getManager()
                ->createQuery($q)
                ->getResult();
        //Todo: cachable query
        
        $placeTypes = $this->getDoctrine()->getManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Model\Place\PlaceType')
                ->findAll();
        //TODO : cachable query
        
        if ($this->getRequest()->getSession()->get('city') !== null)
        {
            $z = $this->getRequest()->getSession()->get('city');
            $managers = $this->getDoctrine()
                    ->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                    ->getGroupsByTypeByCoverage(Group::TYPE_MANAGER, $z->getPolygon());
        } else {
            $managers = array();
        }
        
        $paramsToView = array(
                    'mainCities' => $mainCities, 
                    'cities' => $cities,
                    'categories' => $categories,
                    'placeTypes' => $placeTypes,
                    'managers' => $managers,
                    'terms_allowed' => $terms_allowed_array
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
