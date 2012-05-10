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
        return $this->render('ProgracqteurWikipedaleBundle:Default:homepage.html.twig');
    }
}
