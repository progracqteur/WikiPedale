<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ProgracqteurWikipedaleBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function aboutAction()
    {
        return $this->render('ProgracqteurWikipedaleBundle:Default:about.html.twig');
    }
    
    public function homepageAction()
    {
        
        if ($this->getRequest()->getSession()->get('city') == null)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $cities = $em->createQuery("select c from ProgracqteurWikipedaleBundle:Management\\City c order by c.name")
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


        
        
        return $this->render('ProgracqteurWikipedaleBundle:Default:homepage.html.twig', array('mainCities' => $mainCities, 'cities' => $cities));
    }
}
