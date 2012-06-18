<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Description of NormalizerSerializerService
 *
 * @author julien
 */
class NormalizerSerializerService {
    
    const JSON_FORMAT = 'json';
    
    private $em;
    
    //Normalizers
    private $addressNormalizer = null;
    private $placeNormalizer = null;
    private $userNormalizer = null;
    private $normalizedResponseNormalizer = null;
    
    
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getAddressNormalizer()
    {
        if ($this->addressNormalizer === null)
        {
            $this->addressNormalizer = new AddressNormalizer($this);
        }
        
        return $this->addressNormalizer;
    }
    
    /**
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer 
     */
    public function getPlaceNormalizer()
    {
        if ($this->placeNormalizer === null)
        {
            $this->placeNormalizer = new PlaceNormalizer($this);
        }
        
        return $this->placeNormalizer;
    }
    
    public function getUserNormalizer()
    {
        if ($this->userNormalizer === null)
        {
            $this->userNormalizer = new UserNormalizer($this);
        }
        
        return $this->userNormalizer;
    }
    
    public function getNormalizedResponseNormalizer()
    {
        if ($this->normalizedResponseNormalizer === null)
        {
            $this->normalizedResponseNormalizer = new NormalizedResponseNormalizer($this);
        }
        
        return $this->normalizedResponseNormalizer;
    }
    
    public function getManager()
    {
        return $this->em;
    }
    
    public function getSession()
    {
        return $this->session;
    }
    
    public function serialize($response, $format)
    {
        switch($format)
        {
            case self::JSON_FORMAT :
                $encoder = new JsonEncoder();
                break;
            default :
                throw new \Exception("Le format $format n'est pas connu par le service NormalizerSerializerService");
        }
        
        $serializer = new Serializer(array($this->getNormalizedResponseNormalizer()), array($format => $encoder) );
        return $serializer->serialize($response, $format);
        
        
    }
    
}

