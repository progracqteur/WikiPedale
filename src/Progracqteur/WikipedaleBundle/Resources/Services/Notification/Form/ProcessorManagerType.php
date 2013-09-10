<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services\Notification\Form;

use Symfony\Component\Form\AbstractType;
use Progracqteur\WikipedaleBundle\Form\Management\Notification\FrequencyType;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Doctrine\ORM\EntityRepository;

/**
 * Description of ProcessorModeratorType
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ProcessorManagerType extends AbstractType {
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    private $user;
    
    public function __construct(User $user) {
        $this->user = $user;
    }
    
    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options) {
        parent::buildForm($builder, $options);
        
        $builder->add('frequency', 'notification_frequency' , array(
            'label' => 'notification_subscriptions.forms.frequency.label'
        ));
        
        $groups = $this->user->getGroups();
        
        $groupsManager = array();
        
        foreach ($groups  as $group) {
            if ($group->getType() === Group::TYPE_MANAGER) {
                $groupsManager[] = $group;
            }
        }
        
        if (count($groupsManager) > 1) {
            $builder->add('zone', 'entity', array(
                'class' => 'ProgracqteurWikipedaleBundle:Management\Zone',
                'query_builder' => function(EntityRepository $er) use ($groupsManager) {
                    $inParam = array();
                    
                    foreach ($groupsManager as $group) {
                        $inParam[] = $group->getZone()->getId();
                    }
                    
                    $qb = $er->createQueryBuilder('z');
                    $qb->add('where', 
                                $qb->expr()->in('z.id', $inParam)
                                )
                        ->orderBy('z.slug');
                    
                    return $qb;
                }
            ));
        }
        
    }
    
    public function getName() {
        return 'processor_moderator_type';
    }    
}

