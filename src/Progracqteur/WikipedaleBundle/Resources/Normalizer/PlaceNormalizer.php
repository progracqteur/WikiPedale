<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;



/**
 * Description of PlaceNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class PlaceNormalizer implements NormalizerInterface {
    
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
        
        if ($data['id'] === null){
            $p = new Place();
        }
        else {
            $p = $this->service->getManager()
                    ->getRepository('ProgracqteurWikipedaleBundle:Model\\Place')
                    ->find($p['id']);
            
            if ($p === null)
            {
                throw new \Exception("La place recherchée n'existe pas");
            }
        }
        
        $p->setDescription($data['description']);
        $point = Point::fromArrayGeoJson($data['geom']);
        $p->setGeom($point);
        
        $addrNormalizer = $this->service->getAddressNormalizer();
        $addr = $addrNormalizer->denormalize($data['addressparts'], 
                $this->service->returnFullClassName(NormalizerSerializerService::ADDRESS_TYPE), 
                $format);
        $p->setAddress($addr);
        
        $userNormalizer = $this->service->getUserNormalizer();
        $u = $userNormalizer->denormalize($data['creator'], 
                $this->service->returnFullClassName(NormalizerSerializerService::USER_TYPE), 
                $format);
        $p->setCreator($u);
        
        return $p;
    }
    
    public function normalize($object, $format = null) {
        $creator = $object->getCreator();
        $addrNormalizer = $this->service->getAddressNormalizer();
        $userNormalizer = $this->service->getUserNormalizer();
        return array(
            'entity' => 'place',
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
    
    public function supportsDenormalization($data, $type, $format = null) 
    {
        if ($data['entity'] == 'place')
        {
            return true;
        } else 
        {
            return false;
        }
        
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

