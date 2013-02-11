<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Progracqteur\WikipedaleBundle\Entity\Management\Notation;

/**
 * Description of LoadNotationData
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class LoadNotationData extends AbstractFixture implements OrderedFixtureInterface {
    
    
    public function getOrder() {
        return 2;
    }

    public function load(ObjectManager $manager) {
        $notations = array('gracq', "spw", "villedemons", 'cem');
        
        foreach ($notations as $name)
        {
            $n = new Notation($name);
            switch ($name) {
                case 'gracq' : 
                    $l = "Locale du GRACQ";
                    break;
                case 'spw' : 
                    $l = "Service public de Wallonie";
                    break;
                case 'villedemons' :
                    $l = "Ville de Mons";
                    break;
                case 'cem':
                    $l = "Conseiller en mobilité local";
                    
            }
            $n->setName($l);
            $manager->persist($n);
            $this->addReference('notation_'.$n->getId(), $n);
            echo "ajout de la catégorie notation_".$n->getId()."\n";
            
        }
        
        $manager->flush();
        
    }

    
}

