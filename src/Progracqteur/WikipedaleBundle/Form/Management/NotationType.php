<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class NotationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('name')
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_notationtype';
    }
}
