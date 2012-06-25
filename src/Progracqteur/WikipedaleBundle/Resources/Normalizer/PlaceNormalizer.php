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
        
        if (isset($data['description']))
            $p->setDescription($data['description']);
        
        if (isset($data['geom'])) 
        {
            $point = Point::fromArrayGeoJson($data['geom']);
            $p->setGeom($point);
        }
        
        if (isset($data['addressparts']))
        {
            $addrNormalizer = $this->service->getAddressNormalizer();
            if ($addrNormalizer->supportsDenormalization($data['addressparts'], 
                    $this->service->returnFullClassName(NormalizerSerializerService::ADDRESS_TYPE), 
                    $format));
            {
                $addr = $addrNormalizer->denormalize($data['addressparts'], 
                        $this->service->returnFullClassName(NormalizerSerializerService::ADDRESS_TYPE), 
                        $format);
                $p->setAddress($addr);
            }
        }
        
        if (isset($data['creator']))
        {
            $userNormalizer = $this->service->getUserNormalizer();
            if ($userNormalizer->supportsDenormalization($data['creator'], 
                    $this->service->returnFullClassName(NormalizerSerializerService::USER_TYPE), 
                    $format))
            {
                $u = $userNormalizer->denormalize($data['creator'], 
                        $this->service->returnFullClassName(NormalizerSerializerService::USER_TYPE), 
                        $format);
                $p->setCreator($u);
            }
        }
        
        if (isset($data['statusBicycle']))
        {
            $p->setStatusBicycle($data['statusBicycle']);
        }
        
        if (isset($data['statusCity']))
        {
            $p->setStatusCity($data['statusCity']);
        }
        
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
        if ($data['entity'] == 'place' && isset($data["id"]))
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

