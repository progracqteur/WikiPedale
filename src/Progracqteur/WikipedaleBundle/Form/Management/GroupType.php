<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use FOS\UserBundle\Form\Type\GroupFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Form\Management\GroupType\ZoneToPolygonTransformer;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;

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
        //$ZoneToPolygonTransformer = new ZoneToPolygonTransformer($this->om);
        
        parent::buildForm($builder, $options);
        
        $builder
            ->add( $builder->create('zone', 'entity', array(
                "class" => 'ProgracqteurWikipedaleBundle:Management\Zone',
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er)
                        {
                            return $er->createQueryBuilder('v')
                                    ->orderBy('v.type', 'ASC')
                                    ->orderBy('v.name', 'ASC');
            
                        },
                'empty_value' => 'admin.form.group.zone.choose_value'
                )
                 
            )
               //    ->prependNormTransformer($ZoneToPolygonTransformer)
            )
            ->add('notation', 'entity', array(
                'class' => 'ProgracqteurWikipedaleBundle:Management\Notation',
                'empty_value' => 'admin.form.group.notation.choose_value',
                'required' => false
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    Group::TYPE_NOTATION => 'Notation',
                    Group::TYPE_MODERATOR => 'Conseiller en mobilité',
                    Group::TYPE_MANAGER => 'Gestionnaire de voirie'
                ),
                'empty_value' => 'admin.form.group.type.choose_value'
            ))
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_grouptype';
    }
}
