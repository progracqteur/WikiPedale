<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;

/**
 * Description of DateNormalizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class DateNormalizer implements NormalizerInterface {
    
    private $service;
    
    public function __construct(NormalizerSerializerService $service) {
        $this->service = $service;
    }
    
    public function denormalize($data, $class, array $format = null) {
        throw new \Exception("not yet implemented");
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Normalizer\DateTime $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null, array $context = array()) {
        $u = intval($object->format('U'), 10);
        return array(
            "entity" => "datetime",
            "u" => $u
        );
    }

    public function supportsDenormalization($data, $type, $format = null, $context = array()) {
        return false; //NOt yet implemented
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Normalizer\DateTime $object
     * @param string $format
     * @return boolean
     */
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof \DateTime)
        {
            return true;
        } else
        {
            return FALSE;
        }
    }
}

