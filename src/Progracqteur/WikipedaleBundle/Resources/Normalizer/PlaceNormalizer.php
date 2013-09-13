<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceStatus;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizingException;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


/**
 * Description of PlaceNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class PlaceNormalizer implements NormalizerInterface, DenormalizerInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;
    
    /**
     * Place being denormalized
     * (useful for recursive denormalization)
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place 
     */
    private $currentPlace;
    
    
    const PLACE_TYPE = 'placetype';
    const MODERATOR_COMMENT = 'moderatorComment';
    const COMMENTS = 'comments';
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }
    
    
    public function denormalize($data, $class, $format = null, array $context = array()) {
        
        if ($data['id'] === null){
            $p = new Place();
        }
        else {
            $p = $this->service->getManager()
                    ->getRepository('ProgracqteurWikipedaleBundle:Model\\Place')
                    ->find($data['id']);
            
            if ($p === null)
            {
                throw new \Exception("La place recherchée n'existe pas");
            }
        }
        
        $this->setCurrentPlace($p);
        
        if (isset($data['description']))
            $p->setDescription($data['description']);
        
        if (isset($data['geom'])) 
        {
            $point = Point::fromArrayGeoJson($data['geom']);
            $p->setGeom($point);
        }
        
        if (isset($data['addressParts']))
        {
            $addrNormalizer = $this->service->getAddressNormalizer();
            if ($addrNormalizer->supportsDenormalization($data['addressParts'], 
                    $class, 
                    $format));
            {
                $addr = $addrNormalizer->denormalize($data['addressParts'], 
                        $class, 
                        $format);
                $p->setAddress($addr);
            }
        }
        
        if (isset($data['creator']))
        {
            $userNormalizer = $this->service->getUserNormalizer();
            if ($userNormalizer->supportsDenormalization($data['creator'], 
                    $class, 
                    $format))
            {
                $u = $userNormalizer->denormalize($data['creator'], 
                        $class, 
                        $format);
                $p->setCreator($u);
            }
        }
        
        if (isset($data['statusBicycle']))
        {
            $p->setStatusBicycle($data['statusBicycle']);
        }
        
        if (isset($data['statusZone']))
        {
            $p->setStatusZone($data['statusZone']);
        }
        
        if (isset($data['accepted']))
        {
            $p->setAccepted($data['accepted']);
        }
        
        if (isset($data['statuses']))
        {
            foreach ($data['statuses'] as $key => $arrayStatus)
            {
                if (is_array($arrayStatus) 
                        && isset($arrayStatus['t']) 
                        && isset($arrayStatus['v']) 
                        )
                {
                    $status = new PlaceStatus();
                    $status->setType($arrayStatus['t'])
                            ->setValue($arrayStatus['v']);
                    $p->addStatus($status);
                }
            }
        }
        
        if (isset($data['categories']))
        {
            $arrayCategories = array();
            foreach($data['categories'] as $cat)
            {
                if ($this->service->getCategoryNormalizer()->supportsDenormalization($cat, $class, $format))
                {
                    $arrayCategories[] = $this->service->getCategoryNormalizer()->denormalize($cat, null);
                } else {
                    throw new NormalizingException('Could not denormalize category '.print_r($cat, true));
                }
            }
            
            
            //at first, check if recorded categories match with a one existing 
            // in the json request. If yes, delete from the request's categories's array
            // if the category is not in the json's request, remove the category
            // from the place.
            foreach ($p->getCategory() as $recordedCat)
            {
                $categoryIsAssociatedWithPlace = false;
                foreach ($arrayCategories as $key => $newCat)
                {
                    if ($newCat->getId() == $recordedCat->getId()) {
                        $categoryIsAssociatedWithPlace = true;
                        unset($arrayCategories[$key]);
                        break;  
                    }
                }
                if ($categoryIsAssociatedWithPlace == false)
                {
                    $p->removeCategory($recordedCat);
                } 
            }
            //add category remaining in json's request: those are new categories
            foreach ($arrayCategories as $newCat)
            {
                $p->addCategory($newCat);
            }
            
        }
        
        if (isset($data['manager']))
        {
            if ($this->service->getGroupNormalizer()
                    ->supportsDenormalization($data['manager'], $class))
            {
                $group = $this->service->getGroupNormalizer()->denormalize($data['manager'], $class);
                $p->setManager($group);
            } else 
            {
                throw new NormalizingException('could not denormalize manager '.$data['manager']);
            }
            
            if ($data['manager'] === null) {
                $p->setManager(null);
            }
                
        }
        
        if (isset($data[self::PLACE_TYPE]))
        {
            if ($this->service
                    ->getPlaceTypeNormalizer()
                    ->supportsDenormalization($data[self::PLACE_TYPE], $class))
            {
                $type = $this->service
                        ->getPlaceTypeNormalizer()
                        ->denormalize($data[self::PLACE_TYPE], $class);
                $p->setType($type);
            } else {
                throw new NormalizingException('could not denormalize placeType');
            }
        }
        
        if (isset($data[self::MODERATOR_COMMENT]))
        {
            $p->setModeratorComment($data[self::MODERATOR_COMMENT]);
        }
        
        return $p;
    }
    
    /**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Place $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null, array $context = array()) {
        $creator = $object->getCreator();
        $addrNormalizer = $this->service->getAddressNormalizer();
        $userNormalizer = $this->service->getUserNormalizer();
        
        //create an array with statuses
        $statuses = $object->getStatuses();
        $s = array();
        
        foreach ($statuses as $status)
        {
            $s[] = array('t' => $status->getType(), 'v' => $status->getValue());
        }
        
        //create an array with categories
        $categories = $object->getCategory();
        $c = array();
        
        foreach ($categories as $cat)
        {
            $c[] = $this->service->getCategoryNormalizer()->normalize($cat, $format);
        }
        
        if ($object->getManager() !== null)
        {
            $manager = $this->service->getGroupNormalizer()->normalize($object->getManager());
        } else {
            $manager = null;
        }
        
        if ($object->getType() !== null)
        {
            $placeType = $this->service
                    ->getPlaceTypeNormalizer()
                    ->normalize($object->getType());
        } else
        {
            $placeType = null;
        }
        
        $nbComments = array(
            'moderator_manager' => $object->getNbComments(Comment::TYPE_MODERATOR_MANAGER),
            'public' => $object->getNbComments(Comment::TYPE_PUBLIC)
            );
        
        return  array(
            'entity' => 'place',
            'description' => $object->getDescription(),
            'geom' => $object->getGeom()->toArrayGeoJson(),
            'id' => $object->getId(),
            'nbComm' => $object->getNbComm(),
            'nbVote' => $object->getNbVote(),
            'creator' => $userNormalizer->normalize($creator, $format),
            'addressParts' => $addrNormalizer->normalize($object->getAddress(), $format),
            'createDate' => $this->service->getDateNormalizer()->normalize($object->getCreateDate(), $format),
            'lastUpdate' => $this->service->getDateNormalizer()->normalize($object->getLastUpdate(), $format),
            'nbPhotos' => $object->getNbPhoto(),
            //'statusBicycle' => $object->getStatusBicycle(),
            //'statusZone' => $object->getStatusZone(),
            'accepted' => $object->isAccepted(),
            'statuses' => $s,
            'categories' => $c,
            'manager' => $manager,
            self::PLACE_TYPE => $placeType,
            self::MODERATOR_COMMENT => $object->getModeratorComment(),
            self::COMMENTS => $nbComments,
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
    
    private function setCurrentPlace(Place $place)
    {
        $this->currentPlace = $place;
    }
    
    /*
     * 
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    public function getCurrentPlace()
    {
        return $this->currentPlace;
    }
}

