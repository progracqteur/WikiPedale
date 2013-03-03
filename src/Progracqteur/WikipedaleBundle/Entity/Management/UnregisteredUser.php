<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of UnregisteredUser
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class UnregisteredUser extends User{
    
    private $ip;
    
    private $check_code = '';
    
    private $check_checked = false;
    
    private $check_checking_date = null;
    
    const ROLE_UNREGISTERED = "UNREGISTERED";
    
    
    
    
    public function __construct($recreate = false)
    {
        parent::__construct();
        $this->confirmed = false;
        
        if ($recreate === false)
        {
            $this->setUncheckedUser();
        }
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
        $u = new self(true);
        $u->setIp($hash->ip);
        $u->setEmail($hash->email);
        $u->setLabel($hash->label);
        $u->setPhonenumber($hash->phonenumber);
        
        $d = new \DateTime($hash->createdate);
        $u->setCreationDate($d);
        
    
            $u->setCheckCode($hash->check_code);

        

            $u->setChecked($hash->check_checked);

        

           // $u->setCheckingDate($hash->check_checking_date);

        
        return $u;
    }
    
    public function toHash()
    {
        $h = new Hash();
        $h->ip = $this->getIp();
        $h->label = $this->getLabel();
        $h->email = $this->getEmail();
        $h->createdate = $this->getCreationDate();
        $h->phonenumber = $this->getPhonenumber();
        $h->check_code = $this->getCheckCode();
        $h->check_checked = $this->check_checked;
        $h->check_checking_date = $this->check_checking_date;
        
        return $h;
    }
    
    public function equals(UserInterface $user)
    {
        if ($user->isRegistered()) {
            return false;
        }
        
        if ($user instanceof UnregisteredUser)
        {
            return $user->toHash()->equals($this->toHash());
        } else {
            return false;
        }
    }
    
    public function getRoles(){
        return array(self::ROLE_UNREGISTERED);
    }
    
    /**
     * This function prepare the user as unchecked 
     * 
     * - a check code is created
     */
    private function setUncheckedUser()
    {
 
        $this->setCheckCode($this->_createCode());
        $this->setChecked(false);
        $this->check_checking_date = null;
        
        return $this;
    }
    
    public function setChecked($checked)
    {
        $this->check_checked = $checked;
        $this->setCheckingDate(new \DateTime());
    }
    
    public function setCheckingDate(\DateTime $date)
    {
        $this->check_checking_date = $date;
    }
    
    /**
     * create a code used to check the creator
     * @return string
     */
    private function _createCode()
    {
        $letters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
        
        $e = "";
        for ($i = 0; $i < 2; $i++)
        {
            $e .= $letters[array_rand($letters)].$numbers[array_rand($numbers)];
        }
        
        return $e;
    }
    
    
    /**
     * return the checking code
     * 
     * @return string
     */
    public function getCheckCode()
    {
        if (isset($this->check_code))
        {
            return $this->check_code;
        } else
        {
            return null;
        }
    }
    
    public function setCheckCode($code)
    {
        $this->check_code = $code;
    }
    
    public function checkCode($code)
    {
        $code_upper = strtoupper($code);
        
        if (isset($this->check_code))
        {
            if ($code_upper === $this->check_code)
            {
                return true;
            } else {
                return false;
            }
        }
        
        return false;
        
    }
       
    
}

