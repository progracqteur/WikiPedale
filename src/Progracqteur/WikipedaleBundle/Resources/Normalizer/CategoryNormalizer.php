<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizingException;

/**
 * Normalize instance of Category
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class CategoryNormalizer implements NormalizerInterface {
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;
    
    
    const ID = 'id';
    const LABEL = 'label';
    const PARENT = 'parent';
    
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
    public function denormalize($data, $class, $format = null) {
        $cat = $this->service->getManager()
                ->getEntityRepository('ProgracqteurWikipedaleBundle:Model/Category')
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
    public function normalize($object, $format = null) {
        $a = array();
        $a[self::LABEL] = $object->getId();
        $a[self::LABEL] = $object->getLabel();
        if ($object->hasParent())
        {
            $a[self::PARENT] = $this->normalize($object->getParent(), $format);
        }
        
        return $a;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return ($data instanceof Category);
    }

    public function supportsNormalization($data, $format = null) {
        return ($data instanceof Category);
    }
}

