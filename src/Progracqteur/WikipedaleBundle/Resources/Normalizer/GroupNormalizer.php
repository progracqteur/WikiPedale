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
    
    /**
     * return null if group id not found on the database
     * 
     * care only on the group's id
     * 
     * @param type $data
     * @param type $class
     * @param type $format
     * @return \Progracqteur\WikipedaleBundle\Entity\Management\Group
     */
    public function denormalize($data, $class, $format = null, array $context = array()) {
        $id = $data['id'];
        
        $group = $this->normalizerService
                ->getManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Management\Group')
                ->find($id);
        
        if ($group == null) 
        {
            return null;
        }
        
        return $group;
    }

    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Group $object
     * @param string $format
     */
    public function normalize($object, $format = null, array $context = array()) {
        
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
                     'notation' => $notation,
                     'id' => $object->getId(),
                );
    }

    public function supportsDenormalization($data, $type, $format = null) {
        if (isset($data['entity']) && $data['entity'] === 'group' && isset($data['id']))
        {
            return true;
        } else
        {
            return false;
        }
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Group)
            return true;
        else
            return false;
    }
}

