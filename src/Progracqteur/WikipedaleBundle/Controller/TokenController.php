<?php


namespace Progracqteur\WikipedaleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
    
    
    public function getNewTokensAction($_format, Request $request)
    {
        /**
         * @var Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface
         */
        $provider = $this->get('progracqteur.wikipedale.token_provider');
        
        $token = $request->request->get('token', null);
        
        if ($token === null)
        {
            $r = new Response('missing parameter token');
            $r->setStatusCode(400);
            return $r;
        }
        
        if ($provider->isCsrfTokenValid('ajax', $token))
        {
            $number = $request->query->get('number', 15);
            $tokens = $provider->getTokens('ajax', $number);
            return new Response(json_encode($tokens));
        }
        else 
        {
            $r = new Response('token provided is not valid');
            $r->setStatusCode(400);
            return $r;
        }
        
    }
    
    
}

