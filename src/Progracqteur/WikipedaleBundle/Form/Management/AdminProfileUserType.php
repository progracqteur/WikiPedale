<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Progracqteur\WikipedaleBundle\Form\Management\ProfileUserType as BaseType;

class AdminProfileUserType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->remove('current_password')
            ->remove('username')
            
        ;
        
        $builder->add('locked', 'checkbox', array(
            'label' => 'admin.profile_user.locked'
            ));
    }

    public function getName()
    {
        return 'wikipedale_user_admin_profile';
    }
}
