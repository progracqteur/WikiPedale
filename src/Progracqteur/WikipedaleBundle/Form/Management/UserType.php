<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class UserType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('label')
            ->add('phonenumber')
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_usertype';
    }
}
