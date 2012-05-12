<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;

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
     * @var hash $infos
     */
    private $infos;

    /**
     * @var integer $nbComment
     */
    private $nbComment;

    /**
     * @var integer $nbVote
     */
    private $nbVote;


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
    public function setCreationDate($creationDate)
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
     * @param hash $infos
     */
    public function setInfos(\hash $infos)
    {
        $this->infos = $infos;
    }

    /**
     * Get infos
     *
     * @return hash 
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * Set nbComment
     *
     * @param integer $nbComment
     */
    public function setNbComment($nbComment)
    {
        $this->nbComment = $nbComment;
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
     * @param integer $nbVote
     */
    public function setNbVote($nbVote)
    {
        $this->nbVote = $nbVote;
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