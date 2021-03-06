<?php

namespace Progracqteur\WikipedaleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of LoadPhotoData
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class LoadPhotoData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     *
     * @var Symfony\Component\DependencyInjection\ContainerInterface 
     */
    private $container;
    
    public function getOrder() {
        return 600;
    }
    public function load(ObjectManager $manager) {
        $path = __DIR__."/Files";
        $handle = opendir($path);
        echo "Traitement des fichiers dans le répertoire: $handle\n";

        $i = 0;
        while (false !== ($entry = readdir($handle))) {
            
            echo "Traitement du fichier ".$path."/".$entry." \n";
            
            try {
                $file = new File($path.'/'.$entry, true);
            } catch (\Exception $exc) {
                echo "Ce fichier est passé \n";
                echo $exc->getMessage()."\n";
                $i++;
                continue;
            }
            
            try {

            $file = new File($path.'/'.$entry, true);
            $photo = $this->container
                    ->get('progracqteurWikipedalePhotoService')
                    ->createPhoto();
            
                $photo->setFile($file);
                $photo->setLegend("Légende de test");
            
            
            if ($i%2 == 0)
            {
                $strRef = "PLACE_FOR_REGISTERED_USER";
            } else {
                $strRef = "PLACE_FOR_UNREGISTERED_USER";
            }
            
            $strRef = $strRef.$i;
            
            if ($this->hasReference($strRef))
            {
                $photo->setPlace($this->getReference($strRef));
                $photo->setCreator($this->getReference('user'));
                $photo->getPlace()->getChangeset()->setAuthor($photo->getCreator());
                
                $errors = $this->container->get('validator')->validate($photo);
                if (count($errors) > 0)
                {
                    $m = "";
                    foreach ($errors as $error)
                    {
                        $m .= $error->getMessage();
                    }
                    throw new \Exception("photo invalide $m");
                }
                

                    $manager->persist($photo);
                
                    $manager->flush();

                
                
                
            }
            
            } catch (\Exception $exc) {
                echo "Fichier passé: $entry \n";
                echo $exc->getMessage()."\n";
                $i++;
                continue;
            } catch (\ErrorException $exc)
            {
                echo "Fichier passé (error exception): $entry \n";
                echo $exc->getMessage()."\n";
                $i++;
                continue;
            }
            
            $i++;
            
            
        }

        

    closedir($handle);
    $manager->flush();

    }

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
}

