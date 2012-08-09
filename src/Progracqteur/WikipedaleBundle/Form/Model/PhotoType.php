<?php

namespace Progracqteur\WikipedaleBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhotoType extends AbstractType
{
    private $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('file', 'file', array('required' => true, 'label' => 'fichier'))
            ->add('legend');
        
        
        
        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $builder->add('published');
        }
        
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_model_phototype';
    }
}
