<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place\PlaceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Description of LoadPlaceTypesData
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class LoadPlaceTypesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface {
    
    private $container ;
    
    public function getOrder() {
        return 433;
    }

    public function load(ObjectManager $manager) {
        $types = array(
            'little' => 'petits problèmes',
            'big' => 'points noirs',
            'middle' => 'moyen terme'
        );
        
        foreach ($types as $key => $t )
        {
            $a = new PlaceType();
            $a->setLabel($t);
            $manager->persist($a);
            
            $this->setReference('type_'.$key, $a);
        }
        
        $manager->flush();
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}

