<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizingException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Normalize instance of Category
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class CategoryNormalizer implements NormalizerInterface, DenormalizerInterface  {
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;
    
    
    const ID = 'id';
    const LABEL = 'label';
    const PARENT = 'parent';
    const ENTITY = 'entity';
    const ENTITY_TYPE = 'category';
    
    public function __construct(NormalizerSerializerService $service) {
        $this->service = $service;
    }
    
    
    /**
     * 
     * @param array $data
     * @param string $class
     * @param string $format
     * @throw \Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizingException
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Category
     */
    public function denormalize($data, $class, $format = null, array $context = array()) {
        $cat = $this->service->getManager()
                ->getRepository('ProgracqteurWikipedaleBundle:Model\Category')
                ->find($data['id']);
        
        if ($cat === null)
        {
            throw new NormalizingException('the category with id '.$data['id'].'is not recorded in database');
        }
        
        return $cat;
    }

    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Category $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null, array $context = array()) {
        $a = array();
        $a[self::ID] = $object->getId();
        $a[self::LABEL] = $object->getLabel();
        $a[self::ENTITY] = self::ENTITY_TYPE;
        if ($object->hasParent())
        {
            $a[self::PARENT] = $this->normalize($object->getParent(), $format);
        }
        
        return $a;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        if (isset($data[self::ENTITY]))
        {
            if ($data[self::ENTITY] == self::ENTITY_TYPE
                    && isset($data[self::ID]))
                return true;
        }
        return false;
    }

    public function supportsNormalization($data, $format = null) {
        return ($data instanceof Category);
    }
}

