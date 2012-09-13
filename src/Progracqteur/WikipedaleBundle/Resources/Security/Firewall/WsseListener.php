<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Progracqteur\WikipedaleBundle\Resources\Security\Authentication\WsseUserToken;

/**
 * Description of WsseListener
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class WsseListener implements ListenerInterface {
    
    protected $securityContext;
    protected $authenticationManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }
    
    public function handle(GetResponseEvent $event) {
        $request = $event->getRequest();
        
        if ($request->headers->has('x-wsse')) {

            $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Created="([^"]+)", Nonce="([^"]+)"/';

            if (preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
                $token = new WsseUserToken();
                $token->setUser($matches[1]);

                $token->digest   = $matches[2];
                $token->nonce    = $matches[4];
                $token->created  = $matches[3];

                try {
                    $returnValue = $this->authenticationManager->authenticate($token);

                    if ($returnValue instanceof TokenInterface) {
                        //TODO mettre à jour le dernier login
                        return $this->securityContext->setToken($returnValue);
                    } elseif ($returnValue instanceof Response) {
                        return $event->setResponse($returnValue);
                    }
                } catch (AuthenticationException $e) {
                    $response = new Response($e->getMessage());
                    $response->setStatusCode(403);
                    $event->setResponse($response);
                }
            }
        
            
            
        }

        
    }
}

