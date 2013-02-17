<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Symfony\Component\Validator\ExecutionContext;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Category
 */
class Category
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $label
     */
    private $label;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $children;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Category
     */
    private $parent;
    
    /**
     *
     * @var boolean
     */
    private $used;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @param string $label
     * @return Category
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
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
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Category $parent
     * @return Category
     */
    public function setParent(\Progracqteur\WikipedaleBundle\Entity\Model\Category $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Category 
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    public function __toString() {
        return $this->getLabel();
    }
    
    public function hasParent()
    {
        return (!($this->parent === null));
    }
    
    /**
     * return true if a category is still in use
     * 
     * @return boolean
     */
    public function isUsed(){
        return $this->used;
    }
    
    /**
     * set if a category may be in used or not
     * 
     * @param boolean $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }
    
    public function isParentAChild(ExecutionContext $context)
    {
        if ($this->hasParent())
        {
            if ($this->getParent()->hasParent())
            {
                if ($this->getParent()->getParent()->hasParent()) {
                    $propertyPath = $context->getPropertyPath().'.parent';
                    $context->setPropertyPath($propertyPath);
                    $context->addViolation('admin.category.form.parent.parent_has_parent', array(), $this->getParent());
                }
            }
        }
    }
    
    public function hasChildren()
    {
        if ($this->getChildren()->count() === 0)
            return false;
        else
            return true;
    }
    
    /**
     * Return an hierarchical view of the label
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Category $category
     * @return string
     */
    public function getHierarchicalLabel() 
    {
        $str = '';
        
        if ($this->hasParent())
            return $this->addParentLabel($str);
        else 
            return $this->getLabel();

    }
    
    /**
     * Recursive function, which add the hierarchical parent label to the
     * string, until the parent category is reached
     * 
     * @param type $string
     * @return type
     */
    public function addParentLabel(&$string)
    {
        if ($this->hasParent())
        {
            return $this->getParent()->addParentLabel($string).' < '.$this->getLabel();
        } else {
            return $this->getLabel().$string;
        }
    }
}