<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\User
 */
class User
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var string $salt
     */
    private $salt;

    /**
     * @var datetime $creationDate
     */
    private $creationDate;

    /**
     * @var boolean $confirmed
     */
    private $confirmed;

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
    
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
        $this->infos = new Hash();
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
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
    public function setSalt($salt)
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
    private function setCreationDate($creationDate)
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
}