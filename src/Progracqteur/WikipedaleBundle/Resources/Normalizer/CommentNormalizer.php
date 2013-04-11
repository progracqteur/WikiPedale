<?php

namespace Progracqteur\WikipedaleBundle\Resources\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Comment;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizingException;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\UserNormalizer;
use Progracqteur\WikipedaleBundle\Resources\Normalizer\PlaceNormalizer;

/**
 * Description of PlaceNormalizer
 *
 * @author julien [at] fastre [point] info & marcducobu [at] gmail [point] com
 */

class CommentNormalizer implements NormalizerInterface {
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Normalizer\NormalizerSerializerService 
     */
    private $service;
    
    /**
     * Comment being denormalized
     * (useful for recursive denormalization)
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Comment
     */
    private $currentComment;
    
    public function __construct(NormalizerSerializerService $service)
    {
        $this->service = $service;
    }

    /*
    public function denormalize($data, $class, $format = null) {
   	if ($data['id'] === null){
            $p = new Comment();
        }

    }
    else {
            $p = $this->service->getManager()
                    ->getRepository('ProgracqteurWikipedaleBundle:Model\\Comment')
                    ->find($data['id']);
            
            if ($p === null)
            {
                throw new \Exception("La place recherchée n'existe pas");
            }
        }

    $this->setCurrentComment($p);

    if (isset($data['text']))
            $p->setText($data['text']);

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

    if (isset($data['published']))
        {
            $p->setPublished($data['published']);
        }

    return $p;
  	}
  	*/



  	/**
     * 
     * @param \Progracqteur\WikipedaleBundle\Entity\Model\Comment $object
     * @param string $format
     * @return array
     */
    public function normalize($object, $format = null) {    
        return  array(
            'entity' => 'comment',
            'id' => $object->getId(),
            'text' => $object->getText(),
            'published' => $object->isPublished(),
            'creationDate' => $this->service->getDateNormalizer()->normalize($object->getCreationDate(), $format),
            'creator' => $this->service->getUserNormalizer()->normalize($object->getCreator(), $format),
            //'place' => $this->service->getPlaceNormalizer()->normalize($object->getPlace(), $format),
            'placeId' => $object->getPlace()->getId(),
        );
    }
    
    public function supportsNormalization($data, $format = null) {
        if ($data instanceof Comment)
        {
            return true;
        } else
        {
            return false;
        }
    }

    public function supportsDenormalization($data, $type, $format = null) 
    {
        if ($data['entity'] == 'comment')
        {
            return false; //true;
        } else 
        {
            return false;
        }
        
    }
    
    private function setCurrentComment(Comment $comment)
    {
        $this->currentComment = $comment;
    }
    
    /*
     * 
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Comment
     */
    public function getCurrentComment()
    {
        return $this->currentComment;
    }
}

