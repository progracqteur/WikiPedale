<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Security\ChangeService;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
/**
 * normalizer PlaceTracking elements to an array, and back. 
 * Used with Serializer.
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTrackingNormalizer implements NormalizerInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;


    public function __construct(NormalizerSerializerService $service) {
        $this->service = $service;
    }
    
    
    public function denormalize($data, $class, $format = null) {
        throw new \Exception("denormalization of a placeTracking is forbidden");
    }

    /**
     * 
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceTracking $object
     * @param string $format
     */
    public function normalize($object, $format = null) {
        
        $userNormalizer = $this->service->getUserNormalizer();
        
        $a = array(
          'id' => $object->getId(),
          'entity' => 'placeTracking',
          'date' => $this->service->getDateNormalizer()->normalize($object->getDate(), $format),
          'isCreation' => $object->isCreation(),
          'author' => $userNormalizer->normalize($object->getAuthor()),
          'placeId' => $object->getPlace()->getId()
        );
        
        $changes = array();
        
        if ( !$object->isCreation()) //si le tracking est une création, alors on n'envoie pas les détails
        {
            foreach ($object as $change)
            {
                switch($change->getType())
                {
                    case ChangeService::PLACE_ADDRESS :
                        
                        $value = $this->service->getAddressNormalizer()->normalize($change->getNewValue());
                        /*$h = $change->getNewValue();
                        
                        $value = $this->service->getAddressNormalizer()->normalize($adresse, $format);
                        */
                        break;
                        
                    case ChangeService::PLACE_ADD_PHOTO : 
                        $value = $change->getNewValue(); //on garde le filename de la photo
                        break;
                    case ChangeService::PLACE_GEOM :
                        $value = $change->getNewValue()->toArrayGeoJson();
                        break;
                    default:
                        $value = $change->getNewValue();
                }



                $changes[] = array('type' => $change->getType(),
                    'newValue' => $value);
            }
        }
        
        
        $a['changes'] = $changes;
        
        return $a;
    }

    public function supportsDenormalization($data, $type, $format = null) {
        return false; //la dénormalisation n'a pas de sens
    }

    public function supportsNormalization($data, $format = null) {
        if ($data instanceof PlaceTracking)
        {
            return true;
        }
        
        return false;
    }
}

