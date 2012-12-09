<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Description of WsseUserToken
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class WsseUserToken extends AbstractToken {
    
    public $created;
    public $digest;
    public $nonce;
    
    private $is_fully_authenticated = false;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return '';
    }
    
    public function setFullyAuthenticated()
    {
        $this->is_fully_authenticated = true;
    }
    
    public function isFullyAuthenticated()
    {
        return $this->is_fully_authenticated;
    }
    
    
}

