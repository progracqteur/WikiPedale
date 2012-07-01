<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedExceptionResponse;

/**
 * Description of NormalizedExceptionResponseNormalizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NormalizedExceptionResponseNormalizer implements NormalizerInterface {
    
    public function denormalize($data, $class, $format = null) {
        
    }
    
    public function normalize($object, $format = null) {
        return array(
            'error' => true,
            'message' => $object->getException()->getMessage()
        );
    }
    
    public function supportsDenormalization($data, $type, $format = null) {
        return false;
    }
    
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof NormalizedExceptionResponse)
        {
            return true;
        } else {
            return false;
        }
    }
}

