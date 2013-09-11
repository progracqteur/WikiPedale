<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;


/**
 * Description of AddressNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class AddressNormalizer implements NormalizerInterface, Symfony\Component\Serializer\Normalizer\DenormalizerInterface {
    
    private $service;
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }
    
    public function denormalize($data, $class, $format = null, array $context = array()) {
        $a = new Address();
        
        if (isset($data['road']))
            $a->setRoad($data['road']);
        
        return $a;
    }
    
    public function normalize($object, $format = null, array $context = array()) {
        $array = $object->toArray();
        $array['entity'] = 'address';
        return $array;
    }
    
    public function supportsDenormalization($data, $type, $format = null) {
        if ($data['entity'] == 'address')
        {
            return true;
        } else 
        {
            return false;
        }
        
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

