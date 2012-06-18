<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;

/**
 * Description of NormalizedResponseNormalizer
 *
 * @author julien
 */
class NormalizedResponseNormalizer implements NormalizerInterface
{
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }

    public function denormalize($data, $class, $format = null) {
        
    }

    /*
     * 
     */
    public function normalize($object, $format = null) {
        $a = array();
        $a['nb'] = $object->getCount();
        $a['start'] = $object->getStart();
        
        if ($object->getTotal() > 0)
        {
            $a['total'] = $object->getTotal();
        }
        
        if ($object->getLimit() > 0)
        {
            $a['limit'] = $object->getLimit();
        }
        
        $r['query'] = $a;
        
        $b = array();
        
        //traitement des résultats
        $result = $object->getResults();
       
            foreach ($result as $key => $object)
            {
                $b[] = $this->getNormalizedForm($object);
            }
         
        $r['results'] = $b;
        
        return $r;
       
        
    }
    
    private function getNormalizedForm($object)
    {
        if ($object instanceof Place)
        {
            return $this->service->getPlaceNormalizer()->normalize($object);
        } else if ($object instanceof User)
        {
            return $this->service->getUserNormalizer()->normalize($object);
        }
    }

    public function supportsDenormalization($data, $type, $format = null) {
        
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof NormalizedResponse)
        {
            return true;
        } else
        {
            return false;
        }
    }
}

