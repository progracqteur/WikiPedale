<?php

namespace Progracqteur\WikipedaleBundle\Form\Management\GroupType;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Management\City;

/**
 * Description of CityToPolygonTransformer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class CityToPolygonTransformer implements DataTransformerInterface {
    
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
        $city = $this->om->getRepository('ProgracqteurWikipedaleBundle:Management\City')
                ->find($id);
        return $city->getPolygon();
    }
    
    /**
     * This function does nothing... 
     * 
     * @param type $city
     * @return string
     */
    public function transform($polygonString) {
        
        if ($polygonString === null)
        {
            return null;
        }
        
        $city = $this->om->getRepository('ProgracqteurWikipedaleBundle:Management\City')
                ->findOneByPolygon($polygonString);
        return $city;
    }
}

