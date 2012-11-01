<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use Doctrine\ORM\EntityManager;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * This service is a tool for geographic operation where the database is
 * needed.
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class GeoService {
    
    /**
     *
     * @var Doctrine\ORM\EntityManager 
     */
    private $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * This function return the postgis'string of a Geographic Point.
     * 
     * It use a postgis database request to convert the point to the postgis string
     * 
     * @param \Progracqteur\WikipedaleBundle\Resources\Geo\Point $geog
     * @return string
     */
    public function toString($geog)
    {
        if ($geog instanceof Point)
        {
            $wkt = $geog->toWKT();
            
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('string', 'string');
            
            $q = $this->em->createNativeQuery('SELECT ST_GeographyFromText(:wkt) as string ', $rsm)
                ->setParameter('wkt', $wkt);
            $r = $q->getResult();
            
            
            return $r[0]['string'];
        } else {
            throw new \Exception('object not supported');
        }
    }
    
}

