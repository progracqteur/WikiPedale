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
        
        $petit = new Category();
        $petit->setLabel("Petits problèmes");
        $manager->persist($petit);
        
        
        $revetement = new Category();
        $revetement->setLabel("Revêtement");
        $revetement->setParent($petit);
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
        $signal->setParent($petit);
        $manager->persist($signal);
        
        $feu = new Category();
        $feu->setLabel("Feu en panne")
                ->setParent($signal);
        $manager->persist($feu);
        
        $this->addReference('cat3', $feu);
        
        $gros = new Category();
        $gros->setLabel('Point noir');
        $manager->persist($gros);
        
        $carrefour = new Category();
        $carrefour->setLabel("Carrefours dangereux")
                ->setParent($gros);
        $manager->persist($carrefour);
        
        $cycl = new Category();
        $cycl->setLabel("Pas de place au cycliste")
                ->setParent($carrefour);
        $manager->persist($cycl);
        
        $manager->flush();
        
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function getOrder() {
        return 4;
    }
}

