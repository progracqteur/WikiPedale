<?php

namespace Progracqteur\WikipedaleBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;

class CategoryType extends AbstractType
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        //create a choice list for categories
        $c = $this->em->createQuery('SELECT c 
            FROM ProgracqteurWikipedaleBundle:Model\Category c JOIN c.parent p 
            WHERE p.parent is null OR c.parent is null
            ')
                ->getResult();
        
        
        $choiceList = new ArrayChoiceList(function() use ($c) 
            {
                $a = array();
                
                foreach ($c as $category)
                {
                    $a[$category->getId()] = CategoryType::printLabel($category);
                }
                
                return $a;
            }
        );
        
        $builder
            ->add('label')
            ->add('parent', 'choice', array(
                'choice_list' => $choiceList,
                'empty_value' => 'admin.category.form.parent.empty_value',
                'required' => false
            ))
            ->add('used', 'choice', array(
                'choices' => array(
                    true => 'admin.category.form.field.used.in_use',
                    false => 'admin.category.form.field.used.not_in_use'
                    ),
                'multiple' => false,
                'expanded' => true
            ))
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_model_categorytype';
    }
    
    public static function printLabel(Category $category) 
    {
        $str = '';
        if ($category->hasParent())
        {
            $str .= $category->getParent()->getLabel()." > ";
        }

        $str .= $category->getLabel();

        return $str;
    }
}
