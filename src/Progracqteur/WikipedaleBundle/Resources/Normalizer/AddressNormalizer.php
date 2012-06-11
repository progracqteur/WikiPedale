<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;


/**
 * Description of AddressNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class AddressNormalizer implements NormalizerInterface {
    
    public function denormalize($data, $class, $format = null) {
        return null;
    }
    
    public function normalize($object, $format = null) {
        $array = $object->toArray();
        return $array;
    }
    
    public function supportsDenormalization($data, $type, $format = null) {
        return false;
        
    }
    
    public function supportsNormalization($data, $format = null) {
        
        if ($data instanceof Address) {
            return true;
        } else
        {
            return false;
        }
        
    }
}

