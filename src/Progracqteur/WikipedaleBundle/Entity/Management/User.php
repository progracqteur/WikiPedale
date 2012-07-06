<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\User
 */
class User extends BaseUser
{

    protected $email = '';

    /**
     * @deprecated
     * @var string $label
     */
    protected $label = '';


    /**
     * @var datetime $creationDate
     */
    protected $creationDate;

    /**
     * @var Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     */
    private $infos;

    /**
     * @var integer $nbComment
     */
    private $nbComment = 0;

    /**
     * @var integer $nbVote
     */
    private $nbVote = 0;
    
    
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_STATUS_BICYCLE = 'BICYCLE';
    const ROLE_STATUS_CITY = 'CITY';
    /**
     * @deprecated 
     */
    const ROLE_REGISTERED = 'REGISTERED';
    
    
    
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setCreationDate(new \DateTime());
        $this->infos = new Hash();
        $salt = md5( uniqid(rand(0,1000), true) );
        $this->setSalt($salt);
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set label
     * @deprecated
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->setUsername($label);
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->getUsername();
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    private function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set creationDate
     *
     * @param datetime $creationDate
     */
    protected function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * Get creationDate
     *
     * @return datetime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set confirmed
     *
     * @param boolean $confirmed
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * Get confirmed
     *
     * @return boolean 
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Set infos
     *
     * @param Progracqteur\WikipedaleBundle\Resources\Container\Hash $infos
     */
    public function setInfos(Hash $infos)
    {
        $this->infos = $infos;
    }

    /**
     * Get infos
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set nbComment
     *
     * 
     */
    public function increaseNbComment()
    {
        $this->nbComment++;
    }

    /**
     * Get nbComment
     *
     * @return integer 
     */
    public function getNbComment()
    {
        return $this->nbComment;
    }

    /**
     * Set nbVote
     *
     * 
     */
    public function increaseNbVote($nbVote)
    {
        $this->nbVote++;
    }

    /**
     * Get nbVote
     *
     * @return integer 
     */
    public function getNbVote()
    {
        return $this->nbVote;
    }
    
        public function isRegistered()
    {
        return true;
    }

    public function equals(UserInterface $user) {
        if (!($user instanceof UnregisteredUser))
            return false;
        else {
            return parent::equals($user);
        }
    }
}