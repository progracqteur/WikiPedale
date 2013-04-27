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
            ->add('label', 'text', array('label' => "user.form.registration.fields.label"))
            ->add('phonenumber', 'text', array('label' => 'user.form.registration.fields.phonenumber'))
        ;
    }

    public function getName()
    {
        return 'wikipedale_user_registration';
    }
}
