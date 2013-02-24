<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Zone;

/**
 * Description of LoadCitiesData
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class LoadCitiesData extends AbstractFixture implements ContainerAwareInterface, 
        OrderedFixtureInterface 
{
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $container;
    
    public function getOrder() {
        return 1;
    }

    public function load(ObjectManager $manager) {
        /**
         * @var \Doctrine\ORM\EntityManager
         */
        $em = $this->container->get('doctrine.orm.entity_manager');
        
        $nbCities = $em->createQuery("SELECT count(c.id) from 
            ProgracqteurWikipedaleBundle:Management\Zone c")
                ->getSingleScalarResult();
        
        if ($nbCities == 0) {
            
            $conn = $em->getConnection();
            $r = $conn->executeUpdate("insert into zones 
                (id, name, codeprovince, polygon, slug, type, center)  
                select gid, nom, nurgcdl2, geog, '', '".Zone::TYPE_CITY."', 
                        ST_Centroid(ST_geomFromText(ST_AsText(geog))) 
                        from limites where nom is not null;");
            echo "$r cities added to the database \n";
            
            echo "ajout données du SPW \n";
            $r = $conn->executeUpdate("insert into zones 
                (id, name, codeprovince, polygon, slug, type, center)  
                select gid+1000, nom, '', geog, '', '".Zone::TYPE_SPW."', 
                        ST_Centroid(ST_geomFromText(ST_AsText(geog))) 
                        from limites_spw where nom is not null;");
            echo "$r zones spw added to the database \n";
            $r = $conn->executeUpdate("update zones set slug = getslug(name);");
            echo "$r slug updated in the database \n";
            
        } else {
            echo "$nbCities records as cities. No update done. \n";
        }
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}

