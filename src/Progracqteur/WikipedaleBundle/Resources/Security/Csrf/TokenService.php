<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security\Csrf;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Session;

/**
 * This class generate tokens for csrf protection. It is intended for using token
 * in ajax request.
 * 
 * In the base html code, a large number of tokens (10-15 tokens) are provided. 
 * 
 * On every request, the client must add a token to the request, and may not use 
 * it anymore. When the client does not have enough token, he request new tokens
 * with one of the last existant ones.
 * 
 * It use an existant token provider service for storing tokens.
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class TokenService implements CsrfProviderInterface {
    
    /**
     *
     * @var string 
     */
    private $salt;
    
    /**
     *
     * @var Symfony\Component\HttpFoundation\Session 
     */
    private $session;
    
    const SESSION_KEY_INDEX = 'token_ajax';
    
    
    public function __construct(Session $session, $salt)
    {
        $this->salt = $salt;
        $this->session = $session;
    }
    
    public function getTokens($intention, $number = 15)
    {
        $a = $this->session->get(self::SESSION_KEY_INDEX, null);
        
        if ($a === null)
        {
            $a = array();
        }
        
        for ($i = 0; $i < $number; $i++)
        {
            $a[$intention][] = $this->generateToken();
        }
        
        $this->session->set(self::SESSION_KEY_INDEX, $a);
        
        return $a[$intention];
    }

    public function generateCsrfToken($intention) {
        $a = $this->session->get(self::SESSION_KEY_INDEX, null);
        
        $token = $this->generateToken();
        
        $a[$intention][] = $token;
        
        $this->session->set(self::SESSION_KEY_INDEX, $a);
        
        return $token; 
    }

    public function isCsrfTokenValid($intention, $token) {
        $a = $this->session->get(self::SESSION_KEY_INDEX, array());
        
        $bool = in_array($token, $a[$intention]);
        
        if($bool)
        {
            foreach ($a[$intention] as $key => $value)
            {
                if ($value === $token)
                {
                    unset($a[$intention], $key);
                }
            }
            
            $this->session->set(self::SESSION_KEY_INDEX, $a);
        }
        
        return $bool;
        
    }
    
    private function generateToken()
    {
        return sha1(uniqid($this->salt.rand()));
    }
    
    
    
    
}

