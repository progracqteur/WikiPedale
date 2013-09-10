<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;

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

    public function denormalize($data, $class, $format = null, array $context = array()) {
        
    }

    /*
     * 
     */
    public function normalize($object, $format = null, array $context = array()) {
        $a = array();
        $a['error'] = false;
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
        
        if (is_array($result)) {
            foreach ($result as $key => $data)
            {
                $b[] = $this->getNormalizedForm($data);
            }
        } else {
            $b[] = $this->getNormalizedForm($result);
        }
         
        $r['results'] = $b;
        
        if ($object->hasUser())
        {
            $this->service->getUserNormalizer()->addGroupsToNormalization(true);
            $r['user'] = $this->getNormalizedForm($object->getUser());
            $this->service->getUserNormalizer()->addGroupsToNormalization(false);
        }
        
        return $r;
       
        
    }
    
    /**
     * 
     * @param mixed $object the object to normalize
     * @return array
     */
    private function getNormalizedForm($object)
    {
        if ($object instanceof Place)
        {
            return $this->service->getPlaceNormalizer()->normalize($object);
        } elseif ($object instanceof User)
        {
            return $this->service->getUserNormalizer()->normalize($object);
        } elseif ($object instanceof Comment)
        {
            return $this->service->getCommentNormalizer()->normalize($object);
        } elseif ($object instanceof Photo)
        {
            return $this->service->getPhotoNormalizer()->normalize($object);
        } elseif ($object instanceof PlaceTracking)
        {
            return $this->service->getPlaceTrackingNormalizer()->normalize($object);
        } elseif  ($object instanceof Category) 
        {
            return $this->service->getCategoryNormalizer()->normalize($object);
        } elseif ($object instanceof Group)
        {
            return $this->service->getGroupNormalizer()->normalize($object);
        } elseif ($object instanceof Zone)
        {
            return $this->service->getZoneNormalizer()->normalize($object);
        }
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false;
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

