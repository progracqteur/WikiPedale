<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification\Form;

use Symfony\Component\Form\AbstractType;
use Progracqteur\WikipedaleBundle\Form\Management\Notification\FrequencyType;

/**
 * Description of ProcessorModeratorType
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ProcessorModeratorType extends AbstractType {
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $builder->add('frequency', 'notification_frequency' , array(
            'label' => 'notification_subscriptions.forms.frequency.label'
        ));
        
    }
    
    public function getName() {
        return 'processor_moderator_type';
    }    
}

