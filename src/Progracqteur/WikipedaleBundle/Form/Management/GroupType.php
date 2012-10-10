<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use FOS\UserBundle\Form\Type\GroupFormType;

class GroupType extends GroupFormType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('polygon')
            ->add('notation')
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_grouptype';
    }
}
