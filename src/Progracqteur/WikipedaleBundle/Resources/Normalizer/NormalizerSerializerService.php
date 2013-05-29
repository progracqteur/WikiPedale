<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\CategoryNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PhotoNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\GroupNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceTypeNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizedResponseNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizedExceptionResponseNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Container\NormalizedResponse;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\DateNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceTrackingNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\CommentNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of NormalizerSerializerService
 *
 * @author julien
 */
class NormalizerSerializerService {
    
    const JSON_FORMAT = 'json';
    const PLACE_TYPE = 'place';
    const ADDRESS_TYPE = 'address';
    const USER_TYPE = 'user';
    const COMMENT_TYPE = 'comment';
    
    private $em;
    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;
    
    private $container;
    
    //Normalizers
    private $addressNormalizer = null;
    private $placeNormalizer = null;
    private $userNormalizer = null;
    private $photoNormalizer = null;
    private $normalizedResponseNormalizer = null;
    private $normalizedResponseExceptionNormalizer = null;
    private $placeTrackingNormalizer = null;
    private $dateNormalizer = null;
    private $groupNormalizer = null;
    private $zoneNormalizer = null;
    private $placeTypeNormalizer = null;
    private $commentNormalizer = null;
    
    /**
     *
     * @var \Progracqteur\WikipedaleBundle\Resources\Normalizer\CategoryNormalizer 
     */
    private $categoryNormalizer = null;
    
    //Encoders
    private $jsonEncoder = null;
    
    
    public function __construct(ContainerInterface $container)//EntityManager $em, $securityContext)
    {
        /*$this->em = $em;
        $this->securityContext = $securityContext;*/
        $this->container = $container;
    }

    /**
     *
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\CommentNormalizer 
     */
    public function getCommentNormalizer()
    {
        if ($this->commentNormalizer === null)
        {
            $this->commentNormalizer = new CommentNormalizer($this);
        }
        
        return $this->commentNormalizer;
    }
    
    /**
     *
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\AddressNormalizer 
     */
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
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\DateNormalizer
     */
    public function getDateNormalizer()
    {
        if ($this->dateNormalizer === null)
        {
            $this->dateNormalizer = new DateNormalizer($this);
        }
        
        return $this->dateNormalizer;
    }
    
    /**
     *
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer 
     */
    public function getPlaceNormalizer()
    {
        if ($this->placeNormalizer === null)
        {
            $this->placeNormalizer = new PlaceNormalizer($this);
        }
        
        return $this->placeNormalizer;
    }
    
    /**
     *
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer 
     */
    public function getUserNormalizer()
    {
        if ($this->userNormalizer === null)
        {
            $this->userNormalizer = new UserNormalizer($this, $this->container->get('libravatar.provider'));
        }
        
        return $this->userNormalizer;
    }
    
    public function getPhotoNormalizer()
    {
        if ($this->photoNormalizer === null)
        {
            $this->photoNormalizer = new PhotoNormalizer($this);
        }
        
        return $this->photoNormalizer;
    }
    
    public function getPlaceTrackingNormalizer()
    {
        if ($this->placeTrackingNormalizer === null)
        {
            $this->placeTrackingNormalizer = new PlaceTrackingNormalizer($this);
        }
        
        return $this->placeTrackingNormalizer;
    }
    
    /**
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\CategoryNormalizer category normalizer
     */
    public function getCategoryNormalizer()
    {
        if ($this->categoryNormalizer === null)
        {
            $this->categoryNormalizer = new CategoryNormalizer($this);
        }
        
        return $this->categoryNormalizer;
        
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\GroupNormalizer
     */
    public function getGroupNormalizer()
    {
        if ($this->groupNormalizer === null)
        {
            $this->groupNormalizer = new GroupNormalizer($this);
        }
        
        return $this->groupNormalizer;
    }
    
    /**
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\ZoneNormalizer
     */
    public function getZoneNormalizer()
    {
        if ($this->zoneNormalizer === null)
        {
            $this->zoneNormalizer = new ZoneNormalizer($this);
        }
        
        return $this->zoneNormalizer;
    }
    
    /**
     * 
     * @return \Progracqteur\WikipedaleBundle\Resources\Normalizer\ZoneNormalizer
     */
    public function getPlaceTypeNormalizer()
    {
        if ($this->placeTypeNormalizer === null)
        {
            $this->placeTypeNormalizer = new PlaceTypeNormalizer($this);
        }
        
        return $this->placeTypeNormalizer;
    }
    
    /**
     *
     * @return Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizedResponseNormalizer 
     */
    public function getNormalizedResponseNormalizer()
    {
        if ($this->normalizedResponseNormalizer === null)
        {
            $this->normalizedResponseNormalizer = new NormalizedResponseNormalizer($this);
        }
        
        return $this->normalizedResponseNormalizer;
    }
    
    public function getNormalizedExceptionResponseNormalizer()
    {
        if ($this->normalizedResponseExceptionNormalizer === null)
        {
            $this->normalizedResponseExceptionNormalizer = new NormalizedExceptionResponseNormalizer();
        }
        
        return $this->normalizedResponseExceptionNormalizer;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        //return $this->em;
        return $this->container->get('doctrine')->getEntityManager();
    }
    
    public function getRequest()
    {
        return $this->container->get('request');
    }
    
    /**
     * 
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     *
     * @return null (?)
     * @deprecated 
     */
    public function getSession()
    {
        return $this->session;
    }
    
    /**
     *
     * @return Symfony\Component\Security\Core\SecurityContext 
     */
    public function getSecurityContext()
    {
        return $this->container->get('security.context');
    }
    
    /**
     *
     * @return Symfony\Component\Serializer\Encoder\JsonEncoder 
     */
    public function getEncoderJson()
    {
        if ($this->jsonEncoder === null)
        {
            $this->jsonEncoder = new JsonEncoder();
        }
        
        return $this->jsonEncoder;
    }
    
    public function returnFullClassName($short_class)
    {
        switch($short_class)
        {
            case self::PLACE_TYPE :
                return 'Progracqteur\\WikipedaleBundle\\Entity\\Model\\Place';
            case self::ADDRESS_TYPE : 
                return 'Progracqteur\\WikipedaleBundle\\Resources\\Container\\Address';
            case self::USER_TYPE : 
                return 'Progracqteur\\WikipedaleBundle\\Entity\\Management\\User';
            case self::COMMENT_TYPE : 
                return 'Progracqteur\\WikipedaleBundle\\Entity\\Model\\Comment';
        }
    }

    
    public function serialize($response, $format)
    {
        switch($format)
        {
            case self::JSON_FORMAT :
                $encoder = $this->getEncoderJson();
                break;
            default :
                throw new \Exception("Le format $format n'est pas connu par le service NormalizerSerializerService");
        }
        
        if ($this->container->get('request')->get('addUserInfo', false))
        {
            if ($this->getSecurityContext()->isGranted('IS_AUTHENTICATED_FULLY')) 
            {
                $response->setUser($this->getSecurityContext()->getToken()->getUser());
            } else {
                $u = new \Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser();
                $response->setUser($u);
            }
        }
        
        $serializer = new Serializer(
                array(
                    $this->getNormalizedResponseNormalizer(),
                    $this->getNormalizedExceptionResponseNormalizer()
                ), 
                array($format => $encoder) );
        return $serializer->serialize($response, $format);
        
        
    }
    
    public function deserialize($string, $type, $format)
    {
        switch($format)
        {
            case self::JSON_FORMAT :
                $encoder = $this->getEncoderJson();
                break;
            default :
                throw new \Exception("Le format $format n'est pas connu par le service NormalizerSerializerService");
        }
        
        switch($type)
        {
            case self::PLACE_TYPE :
                $array = array($this->getPlaceNormalizer());
                break;
            case self::COMMENT_TYPE :
                $array = array($this->getCommentNormalizer());
                break;
            default: 
                throw new \Exception("Le type demandé ($type) est inconnu");
        }
        
        $serializer = new Serializer($array, array($format => $encoder));
        return $serializer->deserialize($string, $type, $format);
    }
    
}

