<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use FOS\UserBundle\Form\Type\GroupFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Form\Management\GroupType\CityToPolygonTransformer;

class GroupType extends GroupFormType
{
    
    private $om;
    
    public function __construct(ObjectManager $om) {
        //the class is injected by the super class into defaultOptions / data_class
        parent::__construct('Progracqteur\WikipedaleBundle\Entity\Management\Group');
        
        $this->om = $om;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        //$cityToPolygonTransformer = new CityToPolygonTransformer($this->om);
        
        parent::buildForm($builder, $options);
        
        $builder
            ->add( $builder->create('city', 'entity', array(
                "class" => 'ProgracqteurWikipedaleBundle:Management\City',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                        {
                            return $er->createQueryBuilder('v')
                                    ->orderBy('v.name', 'ASC');
            
                        },
                'empty_value' => 'city.choose.value'
                )
                 
            )
               //    ->prependNormTransformer($cityToPolygonTransformer)
            )
            ->add('notation', 'entity', array(
                'class' => 'ProgracqteurWikipedaleBundle:Management\Notation',
                'empty_value' => 'notation.choose.value'
            ))
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_grouptype';
    }
}
