<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;



/**
 * Description of PlaceNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class PlaceNormalizer implements NormalizerInterface {
    //put your code here
    public function denormalize($data, $class, $format = null) {
        
    }
    public function normalize($object, $format = null) {
        $creator = $object->getCreator();
        $addrNormalizer = new AddressNormalizer();
        $userNormalizer = new UserNormalizer();
        return array(
            'description' => $object->getDescription(),
            'geom' => $object->getGeom()->toArrayGeoJson(),
            'id' => $object->getId(),
            'nbComm' => $object->getNbComm(),
            'nbVote' => $object->getNbVote(),
            'creator' => $userNormalizer->normalize($creator, $format),
            'addressparts' => $addrNormalizer->normalize($object->getAddress(), $format),
            'createDate' => $object->getCreateDate(),
            'nbPhotos' => $object->getNbPhoto(),
            'statusBicycle' => $object->getStatusBicycle(),
            'statusCity' => $object->getStatusCity()
            
            //TODO: ajouter les autres paramètres
        );
    }
    public function supportsDenormalization($data, $type, $format = null) {
        
    }
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Place)
        {
            return true;
        } else
        {
            return false;
        }
    }
}

