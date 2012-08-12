<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;

/**
 * Description of PhotoNormalizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PhotoNormalizer implements NormalizerInterface {
    
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
        
    }

    public function normalize($object, $format = null) {
        return array(
            'entity' => 'photo',
            'webPath' => $object->getWebPath(),
            'fileName' => $object->getFile(),
            'width' => $object->getWidth(),
            'height' => $object->getHeight(),
            'legend' => $object->getLegend(),
            'creator'=> $this->normalizerService->getUserNormalizer()->normalize($object->getCreator()),
            'placeId' => $object->getPlace()->getId(),
            'published' => $object->getPublished()
        );
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false;
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Photo)
            return true;
        else
            return false;
    }
}

