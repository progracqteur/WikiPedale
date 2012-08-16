<?php

namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ProgracqteurWikipedaleBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function homepageAction()
    {
        
        if ($this->getRequest()->getSession()->get('city') == null)
        {
            $em = $this->getDoctrine()->getEntityManager();

            $cities = $em->createQuery("select c from ProgracqteurWikipedaleBundle:Management\\City c")
                    ->getResult();            
        } else {
            $cities = array();
        }


        
        
        return $this->render('ProgracqteurWikipedaleBundle:Default:homepage.html.twig', array('cities' => $cities));
    }
}
