<?php


namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;




/**
 * Description of UserNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class UserNormalizer implements NormalizerInterface
{
    
    private $service;
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }
    
    
    public function denormalize($data, $class, $format = null) {
        if ($data['id'] === null)
        {
            $u = new UnregisteredUser();
            
            if (isset($data['label']))
                $u->setLabel($data['label']);
            
            if (isset($data['email']))
                $u->setEmail($data['email']);
            
        } else {
            
            $u = $this->service->getManager()
                    ->getRepository('ProgracqteurWikipedaleBundle:Management\\User')
                    ->find($data['id']);
            
            if ($u === null)
            {
                throw new \Exception("L'utilisateur n'a pas été trouvé dans la base de donnée");
            }
        }
        
        return $u;
    }
    
    public function normalize($object, $format = null) {
        
        $a =  array(
            'id' => $object->getId(),
            'label' => $object->getLabel(),
            'nbComment' => $object->getNbComment(),
            'nbVote' => $object->getNbVote(),
            'entity' => 'user',
            'registered' => $object->isRegistered()
        );
        

        return $a;
        
    }
    public function supportsDenormalization($data, $type, $format = null) {
        if ($data['entity'] == 'user' && isset($data['id']))
        {
            return true;
        } else
        {
            return false;
        }
        
        
    }
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof User)
        {
            return true;
        } else {
            return false;
        }
    }
}

