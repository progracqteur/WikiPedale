<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;

/**
 * Description of UnregisteredUser
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class UnregisteredUser extends User{
    
    private $ip;
    
    
    
    
    public function __construct()
    {
        parent::__construct();
        $this->confirmed = false;
    }
    
    public function isRegistered()
    {
        return false;
    }
    
    public function getIp()
    {
        return $this->ip;
    }
    
    public function setIp($ip)
    {
        $this->ip = $ip;
    }
    

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return false;
    }
    
    public static function fromHash(Hash $hash)
    {
        $u = new self();
        $u->setIp($hash->ip);
        $u->setEmail($hash->email);
        $u->setLabel($hash->label);
        
        $d = new \DateTime($hash->createdate);
        $u->setCreationDate($d);
        
        return $u;
    }
    
    public function toHash()
    {
        $h = new Hash();
        $h->ip = $this->getIp();
        $h->label = $this->getLabel();
        $h->email = $this->getEmail();
        $h->createdate = $this->getCreationDate();
        
        return $h;
    }
    
}

