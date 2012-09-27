<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;

/**
 * normalizer PlaceTracking elements to an array, and back. 
 * Used with Serializer.
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTrackingNormalizer implements NormalizerInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;


    public function __construct(NormalizerSerializerService $service) {
        $this->service = $service;
    }
    
    
    public function denormalize($data, $class, $format = null) {
        
    }

    /**
     * 
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking $object
     * @param string $format
     */
    public function normalize($object, $format = null) {
        
        $userNormalizer = $this->service->getUserNormalizer();
        
        $a = array(
          'date' => $object->getDate(),
          'isCreation' => $object->isCreation(),
          'author' => $userNormalizer->normalize($object->getAuthor()),
          'placeId' => $object->getPlace()->getId()
        );
        
        $changes = array();
        
        foreach ($object as $change)
        {
            $changes[] = array('type' => $change->getType(),
                'newValue' => $change->getNewValue());
        }
        
        $a['changes'] = $changes;
        
        return $a;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false; //TODO implémenter normalisation
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof PlaceTracking)
        {
            return true;
        }
        
        return false;
    }
}

