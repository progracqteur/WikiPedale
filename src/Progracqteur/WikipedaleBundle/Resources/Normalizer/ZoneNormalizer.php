<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;

/**
 * Description of DateNormalizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ZoneNormalizer implements NormalizerInterface {
    
    private $service;
    
    public function __construct(NormalizerSerializerService $service) {
        $this->service = $service;
    }
    
    public function denormalize($data, $class, $format = null) {
        throw new \Exception("not yet implemented");
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null) {
        return array(
            'entity' => 'zone',
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'type' => $object->getType()
        );
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false; //NOt yet implemented
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Normalizer\DateTime $object
     * @param string $format
     * @return boolean
     */
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Zone)
        {
            return true;
        } else
        {
            return FALSE;
        }
    }
}

