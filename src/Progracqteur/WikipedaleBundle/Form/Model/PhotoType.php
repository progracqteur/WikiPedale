<?php

namespace Progracqteur\WikipedaleBundle\Form\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PhotoType extends AbstractType
{
    private $container;
    private $newPhoto = false;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        //détermine le champ required du fichier
        switch($this->newPhoto)
        {
            case true:
                $fileRequired = true;
                break;
            case false:
            default:
                $fileRequired = false;
                break;
        }
        
        $builder->add('file', 'file', array('required' => $fileRequired, 'label' => 'Fichier'));
        
        //légende
        $builder->add('legend', 'text', array(
            'label' => "Légende",
            'max_length' => 500
            ));
        
        
        //si l'utilisateur est admin et que c'est pas une nouvelle photo, ajout 
        //de la possibilité de dépublier une photo
        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')
                && $this->newPhoto == false)
        {
            $builder->add('published', 'choice', array(
                'choices' => array(
                    true => 'Publié',
                    false => 'Non publié'
                ),
                'multiple' => false
            ));
        }
        
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_model_phototype';
    }
    
    public function isForANewPhoto($bool)
    {
        $this->newPhoto = $bool;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progracqteur\WikipedaleBundle\Entity\Model\Photo',
            'csrf_protection' => true,
        ));
    }
}
