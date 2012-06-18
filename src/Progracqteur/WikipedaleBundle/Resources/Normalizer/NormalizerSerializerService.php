<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Doctrine\ORM\EntityManager;


/**
 * Description of NormalizerSerializerService
 *
 * @author julien
 */
class NormalizerSerializerService {
    
    private $em;
    
    //Normalizers
    private $addressNormalizer = null;
    private $placeNormalizer = null;
    private $userNormalizer = null;
    
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
    
    public function getManager()
    {
        return $this->em;
    }
    
    public function getSession()
    {
        return $this->session;
    }
    
    public function serialize(NormalizedResponse $response, $format)
    {
        
        
    }
    
}

