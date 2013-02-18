<?php

namespace Progracqteur\WikipedaleBundle\Entity\Management;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\Group as BaseGroup;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Progracqteur\WikipedaleBundle\Entity\Management\Group
 */
class Group extends BaseGroup
{

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Zone $polygon
     */
    private $zone;

    /**
     * @var Progracqteur\WikipedaleBundle\Management\Notation
     */
    private $notation;
    
    /**
     *
     * @var string 
     */
    private $type;
    
    /**
     * @var string
     */
    const TYPE_NOTATION = 'NOTATION';
    /**
     * @var string
     */
    const TYPE_MANAGER = 'MANAGER';
    /**
     * @var string
     */
    const TYPE_MODERATOR = 'MODERATOR';
    
    /**
     * return an array of each valid types
     * 
     * @return array
     */
    public static function getExistingTypes()
    {
        return array(self::TYPE_MANAGER, self::TYPE_MODERATOR, self::TYPE_NOTATION);
    }
    
    

    public function __construct($name = '', $roles = array()) {
        parent::__construct($name, $roles);
    }
    
    /**
     * Set Zone
     *
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $Zone
     * @return Group
     */
    public function setZone(Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    public function setType($type) 
    {
        $this->type = $type;
        
        $this->setRoles(array());
        
        switch ($type) {
            case self::TYPE_MODERATOR :
                $this->addRole(User::ROLE_NOTATION)
                    ->addRole(User::ROLE_CATEGORY)
                    ->addRole(User::ROLE_DETAILS_LITTLE)
                    ->addRole(User::ROLE_PUBLISHED)
                    ->addRole(User::ROLE_SEE_USER_DETAILS)
                    ->addRole(User::ROLE_MANAGER_ALTER)
                        ;
                break;
            case self::TYPE_MANAGER :
                $this->addRole(User::ROLE_NOTATION)
                    ->addRole(User::ROLE_CATEGORY)
                    ->addRole(User::ROLE_DETAILS_LITTLE)
                    ->addRole(User::ROLE_SEE_USER_DETAILS)
                    ->addRole(User::ROLE_MANAGER_ALTER)
                        ;
                break;
            case self::TYPE_NOTATION:
                $this->addRole(User::ROLE_NOTATION)
                    ->addRole(User::ROLE_CATEGORY)
                    ->addRole(User::ROLE_DETAILS_LITTLE)
                    ->addRole(User::ROLE_SEE_USER_DETAILS)
                    ;
                break;
        }
        
        
        return $this;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    
    
    
    /**
     * Set notation
     *
     * @param \Progracqteur\WikipedaleBundle\Management\Notation $notation
     * @return Group
     */
    public function setNotation(\Progracqteur\WikipedaleBundle\Entity\Management\Notation $notation = null)
    {
        $this->notation = $notation;
        return $this;
    }

    /**
     * Get notation
     *
     * @return \Progracqteur\WikipedaleBundle\Management\Notation 
     */
    public function getNotation()
    {
        return $this->notation;
    }
    
    public function __toString() {
        return $this->getName().' ("'.$this->getNotation().'" à '.$this->getZone().')';
    }
    
    
    public function isValidType(ExecutionContext $context) {
        $a = self::getExistingTypes();
        
        if ( ! in_array($this->getType(), $a))
        {
            $propertyPath = $context->getPropertyPath().'Type';
            $context->setPropertyPath($propertyPath);
            $context->addViolation('group.type.invalid', array(), $this->getType());
        }
        
        
    }
    
    public function isValidNotation(ExecutionContext $context)
    {
        $propertyPath = $context->getPropertyPath().'Notation';
        $context->setPropertyPath($propertyPath);
        
        if ($this->hasRole(User::ROLE_NOTATION))
        {
            if ($this->getNotation() === null)
            {
                $context->addViolation('group.notation.not_null', array(), '');
                return;
            }
        }
        
        if ($this->getType() === self::TYPE_MODERATOR)
        {
            if ($this->getNotation()->getId() !== 'cem')
            {
                $context->addViolation('group.notation.must_be_cem', array(), $this->getNotation()->getId());
                return;
            }
        }
    }
}