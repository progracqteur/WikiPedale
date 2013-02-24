<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType;


/**
 * Description of AddressNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class PlaceTypeNormalizer implements NormalizerInterface {
    
    private $service;
    
    const ID = 'id';
    const LABEL = 'label';
    const ENTITY = 'entity';
    const ENTITY_VALUE = 'placetype';
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }
    
    public function denormalize($data, $class, $format = null) {
        $type = $this->service->getManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Model\Place\PlaceType')
                ->find($data[self::ID]);
        return $type;
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null) {
        return array(
                self::ID => $object->getId(),
                self::LABEL => $object->getLabel(),
                self::ENTITY => self::ENTITY_VALUE
                );
        
    }
    
    public function supportsDenormalization($data, $type, $format = null) {

        if ($data[self::ENTITY] == self::ENTITY_VALUE)
        {
            return true;
        } else 
        {
            return false;
        }
        
    }
    
    public function supportsNormalization($data, $format = null) {
        
        if ($data instanceof PlaceType) {
            return true;
        } else
        {
            return false;
        }
        
    }
}

