<?php

namespace Progracqteur\WikipedaleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Progracqteur\WikipedaleBundle\Entity\Model\Place;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;
use Progracqteur\WikipedaleBundle\Entity\Management\UnregisteredUser;
use Doctrine\ORM\Query\ResultSetMapping;
use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of ImportCommand
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class ImportCommand extends ContainerAwareCommand {
    
    const URL = "http://orangeade.be/osm/points_noirs/db_points_noirs.php?all_info=1";
    const URL_PHOTOS = "http://orangeade.be/osm/points_noirs/photo/";


    protected function configure()
    {
        $this->setName('wikipedale:import:orangeade:mons')
                ->setDescription("Importe les données de la version 1.0 vers la version 1.1");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        //import the places from 1.0
        $ch = curl_init(self::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $string = curl_exec($ch);
        curl_close($ch);
        
        $array = json_decode($string);
        
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        
        $sql = "select ST_AsGeoJson(
            St_Transform(
                ST_GeometryFromText(
                   'POINT(%s %s)'
                ,900913)
            ,4326)) as json;"; //reminder : POINT (lon lat)
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('json', 'json');
        
        //array for corresponding between old id and new id
        $corresponding = array();
        
        
        $all_is_true = true;
        
        foreach($array as $old_place)
        {
            $p = new Place();
            $p->setDescription($old_place->description);
            
            $a = new Address();
            $a->setRoad($old_place->lieu);
            
            $u = new UnregisteredUser();
            $u->setUsername($old_place->nom_prenom);
            $u->setEmail($old_place->email);
            $u->setIp('127.0.0.1');
            $p->setCreator($u);
            
            $nsql = $em->createNativeQuery(sprintf($sql, $old_place->lon, $old_place->lat), $rsm);
            $nsql->setParameter('lon',$old_place->lon);
            $nsql->setParameter('lat',$old_place->lat);
            $json_point = $nsql->getSingleScalarResult();
            
            $point = Point::fromGeoJson($json_point);
            
            $p->setGeom($point);
            
            $errors = $this->getContainer()->get('validator')->validate($p);
            
            if ($errors->count() > 0)
            {
                echo "error on place ".$old_place->id." \n";
                foreach ($errors as $error)
                {
                    echo $error->getMessage()."\n";
                    echo "invalid value : ".$error->getInvalidValue()."\n";
                    $all_is_true = false;
                }
            }
            
            $em->persist($p);
            
            //record for correspondance between old id and new id 
            $id = (int) $old_place->id;
            $corresponding[$id] = $p;
            
            //for debugging, do not parse all places
            //break;
            
        }
        
        $ph = curl_init(self::URL_PHOTOS);
        curl_setopt($ph, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ph, CURLOPT_HEADER, false);
        $string = curl_exec($ph);
        curl_close($ph);
        
        $dom = new \DOMDocument();
        $dom->loadHTML($string);
        
        $as = $dom->getElementsByTagName('a');
        
        $photo_pattern = "/[0-9_].jpg/";
        $cache_dir = $this->getContainer()->getParameter('kernel.cache_dir');
        $cache_dir = $cache_dir."/import_photo_from_old_system";
        
        if (file_exists($cache_dir) && is_dir($cache_dir))
        {
            //do nothing
        } else 
        {
            mkdir($cache_dir);
        }
        
        $photo_service = $this->getContainer()->get('progracqteurWikipedalePhotoService');
        $admin = $this->getContainer()->get('fos_user.user_manager')->findUserByUsername('admin');
        
        foreach ($as as $a)
        {
            $href = $a->getAttribute('href');
            
            if (preg_match($photo_pattern, $href) == 0)
            {
                continue;
            }
            
            $match = null;
            preg_match('/([0-9]{1,})_([0-9]{1,}).jpg$/', $href, $match);
            
            $old_place_id = (int) $match[1];
            
            if (isset($corresponding[$old_place_id]))
            {
                echo "Old place id $old_place_id match with picture file $href \n";
                $path = self::URL_PHOTOS.$href;
                
                if (file_exists($cache_dir.'/'.$href))
                {
                    echo "$path already downloaded...";
                } else 
                {
                    echo "downloading $path \n";
                    $ch = curl_init($path);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_VERBOSE, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, "Symfony/update-console");
                    $response = curl_exec($ch);
                    curl_close($ch);

                    file_put_contents($cache_dir.'/'.$href, $response);
                }
                
                $file = new File($cache_dir.'/'.$href, true);
                
                $photo = $photo_service->createPhoto();
                $photo->setFile($file);
                
                echo "new filename is ".$photo->getFile()."\n";
                
                $place = $corresponding[$old_place_id];
                $photo->setPlace($place);
                
                $photo->setCreator($admin);
                
                $errors = $this->getContainer()->get('validator')->validate($photo);
                if ($errors->count() > 0)
                {
                    echo "error on photo ".$href." \n";
                    
                    foreach ($errors as $error)
                    {
                        echo $error->getMessage()."\n";
                        echo "invalid value : ".$error->getInvalidValue()."\n";
                        $all_is_true = false;
                    }
                }
                
                $em->persist($photo);
            } 
        }
        
        //save into database
        //if ($all_is_true)
            $em->flush();
        
        //create an array for explanation of the import :
        $import_array = array();
        foreach ($corresponding as $key => $place)
        {
            $import_array[$key] = $place->getId();
        }
        
        $json = json_encode($import_array);
        echo $json;
        echo "\n";
        
        
        
    }
    
}

