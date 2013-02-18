<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;


/**
 * Description of PhotoNormalizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class GroupNormalizer implements NormalizerInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $normalizerService;


    public function __construct(NormalizerSerializerService $service)
    {
        $this->normalizerService = $service;
    }
    
    
    public function denormalize($data, $class, $format = null) {
        return '';
    }

    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $object
     * @param string $format
     */
    public function normalize($object, $format = null) {
        
        if ($object->getNotation() !== null)
        {
            $notation = $object->getNotation()->getId();
        } else 
        {
            $notation = null;
        }
        
        return array(
                     'entity' => 'group',
                     'type' => $object->getType(),
                     'label' => $object->getName(),
                     'zone' => $this->normalizerService->getZoneNormalizer()->normalize($object->getZone(), $format),
                     'notation' => $notation
                );
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false;
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Group)
            return true;
        else
            return false;
    }
}

