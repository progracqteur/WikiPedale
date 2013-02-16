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
     * the way the user want to be publicly known
     * 
     * @var string $label
     */
    protected $label = '';
    
    /**
     *
     * @var string 
     */
    protected $phonenumber = "";
    


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
    
    /**
     * indicate if the user may create or alter categories
     * 
     * @var string
     */
    const ROLE_CATEGORY = 'ROLE_CATEGORY'; 
    
    /**
     * indicate if the user may create or alter notation. 
     * The group which use it must be associated with a particular notation
     * 
     * @var string
     */
    const ROLE_NOTATION = 'ROLE_NOTATION';
    
    /**
     * @var string
     */
    const ROLE_MANAGER = 'ROLE_MANAGER';
    
    /**
     * @var string
     */
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    
    /**
     * indicate if the user may alter details of a little point
     * 
     * @var string
     */
    const ROLE_DETAILS_LITTLE = 'ROLE_DETAILS_LITTLE';
    
    /**
     * indicate if the user may alter details of a BIG point
     * 
     * @var string
     */
    const ROLE_DETAILS_BIG = 'ROLE_DETAILS_BIG';
    
    /**
     * 
     * indicate if the user may publish/unpublish a place
     * 
     * @var string 
     */
    const ROLE_PUBLISHED = 'ROLE_PUBLISHED';
    
    /**
     * indicate if the user may see email and personal
     * details of other users
     * 
     * @var string
     */
    const ROLE_SEE_USER_DETAILS = 'ROLE_SEE_USER_DETAILS';
    
    
    
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
     * Set label
     * @deprecated
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
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
    
    public function getSalt()
    {
        return '';
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
     * 
     * @param string $phonenumber
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    public function setPhonenumber($phonenumber)
    {
        $this->phonenumber = $phonenumber;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getPhonenumber()
    {
        return $this->phonenumber;
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
        if ($user instanceof UnregisteredUser)
            return false;
        else {
            return parent::equals($user);
        }
    }
}