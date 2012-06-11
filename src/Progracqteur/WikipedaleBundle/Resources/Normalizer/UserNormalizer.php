<?php


namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;




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
        
        return array(
            'id' => $object->getId(),
            'label' => $object->getLabel(),
            'nbComment' => $object->getNbComment(),
            'nbVote' => $object->getNbVote()
        );
        
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

