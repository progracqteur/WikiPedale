<?php

namespace Progracqteur\WikipedaleBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Doctrine\ORM\Query\Expr;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('parent', 'entity', array(
                'class' => 'ProgracqteurWikipedaleBundle:Model\Category',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                {           
                    $qb = $er->createQueryBuilder('c');
                    $qb->leftJoin('c.parent', 'p');
                    $qb->where( $qb->expr()->isNull('c.parent') );
                    $qb->orWhere( $qb->expr()->isNull('p.parent') );
                    $qb->orderBy('c.parent', 'ASC');
                    $qb->orderBy('c.label', 'ASC');

                    return $qb;
                },
                'empty_value' => 'admin.category.form.parent.empty_value',
                'property' => 'hierarchicalLabel',
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
}
