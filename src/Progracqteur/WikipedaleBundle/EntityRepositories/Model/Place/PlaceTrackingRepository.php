<?php

namespace Progracqteur\WikipedaleBundle\EntityRepositories\Model\Place;

use Doctrine\ORM\EntityRepository;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Description of PlaceTrackingRepository
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PlaceTrackingRepository extends EntityRepository {
    
    /**
     * 
     * @param int $first
     * @param int $max
     * @param \Progracqteur\WikipedaleBundle\Entity\Management\Zone $city
     * @param bool $private
     */
    public function getLastEvents($first, $max, Zone $zone, $private = false) {
        
        $sql = "SELECT placetracking.id as id, 
            placetracking.author_id as author_id, 
            placetracking.place_id as place_id, 
            placetracking.iscreation as iscreation, 
            placetracking.details as details, 
            placetracking.date as date
                    FROM placetracking JOIN place on placetracking.place_id = place.id
                    WHERE ST_Covers(:polygonZone, place.geom) 
                    and placetracking.iscreation IS NOT NULL ";
        
        if ($private === false) {
            $sql .= "AND xmlexists('/parent/tree/node[@key=110]' PASSING BY REF details) = false";
        }
        
        $sql.= " ORDER BY date DESC LIMIT :limit OFFSET :offset";
        
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata('ProgracqteurWikipedaleBundle:Model\Place\PlaceTracking', 'pt');
        
        /*$rsm->addEntityResult('ProgracqteurWikipedaleBundle:Model\Place\PlaceTracking', 'pt')
                ->addFieldResult('pt', 'id', 'id')
                ->addFieldResult('pt', 'iscreation', 'isCreation')
                ->addFieldResult('pt', 'date', 'date')
                ->addFieldResult('pt', 'details', 'details')
                ->addMetaResult('pt', 'author_id', 'author', true)
                ->addMetaResult('pt', 'place_id', 'place', true);*/
        
        
        
        return $this->getEntityManager()
                ->createNativeQuery($sql, $rsm)
                ->setParameter('polygonZone', $zone->getPolygon())
                ->setParameter('limit', $max)
                ->setParameter('offset', $first)
                ->getResult();
         
    }
    
}

