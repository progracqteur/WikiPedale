<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;

/**
 * A service that make changeset consistent: it replace the value with proper
 * object extracted from the entity manager
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ChangesetConsistent {
    /**
     *
     * @var Doctrine\Common\Persistence\ObjectManager 
     */
    private $om;
    
    public function __construct(ObjectManager $om) {
        $this->om = $om;
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Security\ChangesetInterface $changeset
     * @return array
     */
    public function getChanges(ChangesetInterface $changeset) {
        if ($changeset instanceof PlaceTracking) {
            $changes = $changeset->getChanges();
            
            //replace the change with proper objects
            foreach ($changes as $key => $value) {
                switch($key) {
                    default: 
                        //do nothing
                        break;
                    case ChangeService::PLACE_PHOTO:
                    case ChangeService::PLACE_ADD_PHOTO:
                        $changes[$key] = $this->om->
                            getRepository('ProgracqteurWikipedaleBundle:Model/Photo')
                                ->findBy(array('filename' => $value));
                        break;
                    case ChangeService::PLACE_ADD_CATEGORY:
                    case ChangeService::PLACE_REMOVE_CATEGORY:
                        //foreach category in array
                        $a = $value; 
                        $b = array();
                        foreach($a as $categoryId) {
                            $b[] = $this->om->getRepository('ProgracqteurWikipedaleBundle:Model/Category')
                                    ->find($categoryId);       
                        }
                        $changes[$key] = $b;
                        break;
                    case ChangeService::PLACE_PLACETYPE_ALTER:
                        $changes[$key] = $this->om->
                        getRepository('ProgracqteurWikipedaleBundle:Model/Place/PlaceType')
                            ->find($value);
                        break;
                }
            }
            
            return $changes;
            
        } else {
            return $changeset->getChanges();
        }
    }
}

