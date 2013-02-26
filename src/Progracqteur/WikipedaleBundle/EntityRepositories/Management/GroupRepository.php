<?php

namespace Progracqteur\WikipedaleBundle\EntityRepositories\Management;

use Doctrine\ORM\EntityRepository;

/**
 * Repository of Entity\Management\Groups
 * 
 * Contains custom function used at multiple places
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class GroupRepository extends EntityRepository {
    
    public function getGroupsByTypeByCoverage($type, $polygon)
    {
        $dql = 'SELECT g from ProgracqteurWikipedaleBundle:Management\Group g 
             JOIN g.zone z
            WHERE Crosses(z.polygon, :zoneB) = true
            AND g.type like :type';
        
        return $this->_em->createQuery($dql)
                ->setParameter('zoneB', $polygon)
                ->setParameter('type', strtoupper($type))
                ->getResult();
        //TODO: query cachable !
    }
    
}

