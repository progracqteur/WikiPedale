<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;

class NotationType extends AbstractType
{
    
    private $type;


    public function __construct($type = 'new')
    {
        $this->type = $type;
        
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($this->type === 'update')
        {
            $builder->add('id', 'text', array('read_only' => true));
        } else {
            $builder->add('id');
        }
        
        $builder
            ->add('name')
        ;
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_notationtype';
    }
}
