<?php

namespace Progracqteur\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('ProgracqteurUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
