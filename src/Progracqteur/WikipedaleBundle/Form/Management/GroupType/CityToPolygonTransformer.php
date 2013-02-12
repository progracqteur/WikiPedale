<?php

namespace Progracqteur\WikipedaleBundle\Form\Management\GroupType;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;

/**
 * Description of ZoneToPolygonTransformer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 * @deprecated since 1.1DEV
 */
class ZoneToPolygonTransformer implements DataTransformerInterface {
    
    /**
     *
     * @var Doctrine\Common\Persistence\ObjectManager 
     */
    private $om;
    
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
    /**
     * 
     * @param type $value
     */
    public function reverseTransform($id) {
        $Zone = $this->om->getRepository('ProgracqteurWikipedaleBundle:Management\Zone')
                ->find($id);
        return $Zone->getPolygon();
    }
    
    /**
     * This function does nothing... 
     * 
     * @param type $Zone
     * @return string
     */
    public function transform($polygonString) {
        
        if ($polygonString === null)
        {
            return null;
        }
        
        $Zone = $this->om->getRepository('ProgracqteurWikipedaleBundle:Management\Zone')
                ->findOneByPolygon($polygonString);
        return $Zone;
    }
}

