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
        
        
        $array = array(
            'revetement' =>
                array(
                    'l' => "Revêtement", "Revêtement dégradé (trous, soulèvement, mal réparé)",
                    'p' => null
                ),
            'revet_degrade' => 
                array(
                    'l' => "Revêtement dégradé (trous, soulèvement, mal réparé)",
                    'p' => 'revetement'
                ),
            'eau_en pluie' =>
                array(
                    'l' => "Accumulation d'eau en cas de pluie",
                    'p' => 'revetement'
                ),
            'bordure saillante' =>
                array(
                    'l' => "Bordure saillante",
                    'p' => 'revetement'
                ),
            'signalisation' =>
                array(
                    'l' => "Signalisation",
                    'p' => null
                ),
            'signalisation manquante' =>
                array(
                    'l' => "Signalisation manquante ou peu claire (balisage, panneau disparu...)",
                    'p' => 'signalisation'
                ),
            'signalisation erronée' =>
                array(
                    'l' => "Signalisation erronée",
                    "p" => "signalisation"
                ),
            'feux en panne' => 
                array(
                    'l' =>"Feux en panne",
                    "p" => "signalisation"
                ),
            'marquage sol effacé' =>
                array(
                    'l' => "Marquages au sol effacés",
                    'p' => "signalisation"
                ),
            'marquage sol incorrect' =>
                array(
                    'l' => "Marquages au sol incorrects",
                    'p' => "signalisation"
                ),
            'obstacle' =>
                array(
                    'l' => "Obstacles",
                    'p' => null
                ),
            'vegetation' =>
                array(
                    'l' => "De la végétation envahit le cheminement cyclable",
                    'p' => 'obstacle'
                ),
            'dechets' => 
                array(
                    'l' => "Des déchets jonchent la voirie (débris de verre, graviers, terre, ...)"
                    ,'p' => 'obstacle'
                ),
            'objets' =>
                array(
                    'l' => "Des objets créent des obstacles sur le cheminement cyclable (poubelle, encombrant)",
                    'p' => 'obstacle'
                ),
            
            'vehicule stationnement illegal' =>
                array(
                    'l' => "Des véhicules sont régulièrement stationnés sur le cheminement cyclable"
                    , "p" => "obstacle"
                ),
            
            'securite' =>
                array(
                    'l' => "Sécurité",
                    "p" => null
                ),
            'eclairage' =>
                array(
                    'l' => "Éclairage public défectueux",
                    'p'=> 'securite'
                ),
            'chantiers' =>
                array(
                    "l" => "Chantiers",
                    "p" => NULL
                ),
            'pas deviation ' =>
                array(
                    'l' => "Pas de prise en compte des cyclistes pour la durée du chantier (déviation, ...)",
                    "p" => "chantiers"
                ),
            'lumiere chantier manquant' =>
                array(
                    'l' => "Signalisation ou balises lumineuses du chantier manquantes/insuffisantes",
                    'p' => 'chantiers'
                ),
            'amenagements disparus apres travaux' =>
                array(
                    'l' => "Aménagements cyclables disparus ou endommagés après les travaux",
                    'p' => "chantiers"
                ),
            'parking' =>
                array(
                    'l' => "Parkings",
                    'p' => null
                ),
            'stationnement velo defectueux' =>
                array(
                    'l' => "Stationnement vélo défectueux, abîmé ou non replacé",
                    'p' => null
                ),
            'autre' =>
                array(
                    'l' => "Autre",
                    "p" => null
                ),
            "autre - select" =>
                array(
                    'l' => "Autre",
                    'p' => 'autre'
                )
            
        );
        
        $categories = array();
        $i = 0;
        
        foreach ($array as $key => $catdef)
        {
            $cat = new Category();
            $categories[$key] = $cat;
            
            $cat->setLabel($catdef['l']);
            if ($catdef['p'] !== null)
            {
                $cat->setParent($categories[$catdef['p']]);
                $this->addReference('cat'.$i, $cat);
                $i++;
            }
            $manager->persist($cat);
        }
        
        $manager->flush();
        
/*
        
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
 * 
 */
        
    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function getOrder() {
        return 400;
    }
}

