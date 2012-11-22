<?php


namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of TokenController
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class TokenController extends Controller{
    
    
    public function insertTokensAction($number = 15)
    {
        $provider = $this->get('progracqteur.wikipedale.token_provider');
        
        $tokens = $provider->getTokens('ajax', $number);
        $string = json_encode($tokens);
        
        return $this->render('ProgracqteurWikipedaleBundle:Token:insertTokens.html.twig', 
                array('string' => $string));
    }
    
    
}

