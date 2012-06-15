<?php


namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;




/**
 * Description of UserNormalizer
 *
 * @author julien [at] fastre [point] info
 */
class UserNormalizer implements NormalizerInterface
{
    //put your code here
    public function denormalize($data, $class, $format = null) {
        
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
        if ($data instanceof User)
        {
            return true;
        } else {
            return false;
        }
        
    }
    public function supportsNormalization($data, $format = null) {
        
    }
}

