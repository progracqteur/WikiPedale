<?php

namespace Progracqteur\WikipedaleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Category;

/**
 * Description of ImportCategories
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ImportCategoriesCommand extends ContainerAwareCommand {
    
    protected function configure()
    {
        $this->setName('uello:categories')
                ->setDescription("Importe les catégories telle que définies par la RW");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
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
                ),
            'test ajout' =>
                array('l' => "test ajout", 'p' => "autre")
        );
        
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $categories = array();
        $i = 0;
        
        foreach ($array as $key => $catdef)
        {
            $existingCategory = $manager
                    ->getRepository('ProgracqteurWikipedaleBundle:Model\Category')
                    ->findOneByLabel($catdef['l']);
            
            if ($existingCategory === null) 
            {
                echo "Ajout de al catégorie ".$catdef['l']." \n";
                $cat = new Category();
                $categories[$key] = $cat;

                $cat->setLabel($catdef['l']);
                if ($catdef['p'] !== null)
                {
                    $cat->setParent($categories[$catdef['p']]);



                    $i++;
                }
                $manager->persist($cat);
            } else {
                $categories[$key] = $existingCategory;
                echo "Catégorie ".$existingCategory->getLabel()." existe déjà ! \n";
            }
        }
        
        $manager->flush();
        
    }
    
}

