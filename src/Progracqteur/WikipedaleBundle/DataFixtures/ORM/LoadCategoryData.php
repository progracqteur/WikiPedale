<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Entity\Management\Group;
use Progracqteur\WikipedaleBundle\Entity\Management\Notation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;

/**
 * Description of LoadCategoryData
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class LoadCategoryData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface {
    
    /**
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $container;
    
    public function load(ObjectManager $manager) {
        $revetement = new Category();
        $revetement->setLabel("Revêtement");
        $manager->persist($revetement);
        
        $mv = new Category();
        $mv->setLabel("Mauvais revêtement")
                ->setParent($revetement);
        $manager->persist($mv);
        
        $this->addReference('cat1', $mv);
        
        $deg = new Category();
        $deg->setLabel("Revêtement dégradé (trous...)")
                ->setParent($revetement);
        $manager->persist($deg);
        
        $this->addReference('cat2', $deg);
        
        $signal = new Category();
        $signal->setLabel("Signalisation");
        $manager->persist($signal);
        
        $feu = new Category();
        $feu->setLabel("Feu en panne")
                ->setParent($signal);
        $manager->persist($feu);
        
        $this->addReference('cat3', $feu);
        
        $manager->flush();
        
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function getOrder() {
        return 4;
    }
}

