<?php

namespace Progracqteur\WikipedaleBundle\Form\Management;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('kind')
            ->add('frequency')
            ->add('transporter')
            ->add('zone')
            ->add('owner')
            ->add('group')
            ->add('groupRef')
            ->add('place')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription'
        ));
    }

    public function getName()
    {
        return 'progracqteur_wikipedalebundle_management_notificationsubscriptiontype';
    }
}
