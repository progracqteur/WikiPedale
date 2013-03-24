<?php

namespace Progracqteur\WikipedaleBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Doctrine\ORM\EntityRepository;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('geom')
            ->add('description')
            ->add(
                    $builder->create('address', 'form', array('by_reference' => true))
                        ->add('road', 'text')
                        ->add('Zone', 'text')
                    )
            ->add('category', 'entity', array(
                'class' => 'ProgracqteurWikipedaleBundle:Model\Category',
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function(EntityRepository $er) {  
                return $er->createQueryBuilder('c')
                            ->orderBy('c.label', 'ASC');
                }
            ))
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_model_placetype';
    }
}
