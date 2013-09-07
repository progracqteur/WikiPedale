<?php

namespace Progracqteur\WikipedaleBundle\Form\Management\Notification;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface as FormBuilder;
use Progracqteur\WikipedaleBundle\Entity\Management\NotificationSubscription;

/**
 * Description of FrequencyType
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class FrequencyType extends AbstractType {
    

    public function setDefaultOptions(\Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                array(
                    'choices' => array(
                            NotificationSubscription::FREQUENCY_MINUTELY 
                                => 'notification_subscriptions.forms.frequency.minutely',
                            NotificationSubscription::FREQUENCY_DAILY 
                                => 'notification_subscriptions.forms.frequency.daily',
                            NotificationSubscription::FREQUENCY_STOPPED
                                => 'notification_subscriptions.forms.frequency.stopped'
                        ),
                    'multiple' => false,
                    'expanded' => true,
                    'preferred_choice' => NotificationSubscription::FREQUENCY_MINUTELY
                )
            );
    }
    
    public function getParent() {
        return 'choice';
    }
    
    public function getName() {
        return 'notification_frequency';
    }    
}

