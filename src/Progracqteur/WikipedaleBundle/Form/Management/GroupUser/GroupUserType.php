<?php

namespace Progracqteur\WikipedaleBundle\Form\Management\GroupUser;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;

/**
 * Form to add or remove group to user
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class GroupUserType extends AbstractType {
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        
        $builder->add('groups', 'entity', array( 
           'class' => 'ProgracqteurWikipedaleBundle:Management\Group',
           'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                            ->orderBy('g.name', 'ASC');
                },
           'expanded' => true,
           'multiple' => true,
        ));
        
        
    }
    
    public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'Progracqteur\WikipedaleBundle\Entity\Management\User'
        );
    }
    
    public function getName() {
        return 'group_user_type';
    }
}

