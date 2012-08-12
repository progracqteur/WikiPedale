<?php

namespace Progracqteur\WikipedaleBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Progracqteur\WikipedaleBundle\Entity\Management\User;
use Progracqteur\WikipedaleBundle\Resources\Services\PhotoService;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Progracqteur\WikipedaleBundle\Entity\Model\Photo
 */
class Photo
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $file
     */
    private $file;

    /**
     * @var datetime $createDate
     */
    private $createDate;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Management\User
     */
    private $creator;

    /**
     * @var Progracqteur\WikipedaleBundle\Entity\Model\Place
     */
    private $place;
    
        /**
     * @var integer $height
     */
    private $height = 0;

    /**
     * @var integer $width
     */
    private $width = 0;

    /**
     * @var string $legend
     */
    private $legend = "";

    /**
     * @var boolean $published
     */
    private $published = true;
    
    /**
     *
     * @var Progracqteur\WikipedaleBundle\Resources\Services\PhotoService 
     */
    private $photoService;
    
    /**
     *
     * @var Symfony\Component\HttpFoundation\File\File 
     */
    private $fileObjectTemp;
    
    /**
     *
     * @var resource 
     */
    private $imageTemp;
    
    private $mustInformPlace = null;
    
    const _ADD_PHOTO = 0;
    const _DELETE_PHOTO = 1;
    const _CHANGE_PLACE = 2;
    private $oldPlace = null;
    
    const MAXIMUM_SIZE = 800;
    const COMPRESSION = 98;
    const TYPE_JPEG = 'jpg';
    
    
    public function __construct()
    {
        $this->setCreateDate(new \DateTime());
    }
    
    public function setPhotoService(PhotoService $service)
    {
        $this->photoService = $service;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set file
     *
     * @param blob $file
     */
    public function setFile(File $file = null)
    { 
        if ($file !== null) 
        {
            $this->fileObjectTemp = $file;
            $this->prepareFileName(self::TYPE_JPEG); //TODO à adapter si support d'autres type d'images
        }
    }
    
    private function prepareFileName($filetype)
    {
        switch ($filetype)
        {
            case self::TYPE_JPEG :
            default:
                $post = ".jpg";
        }
        $this->file = $this->createFileName().$post;
    }

    /**
     * Get file
     *
     * @return blob 
     */
    public function getFile()
    {
        return $this->file;
    }
    
    public function getFileObject()
    {
        return $this->fileObjectTemp;
    }
    
    /**
     * exécutée durant la phase de prePersist de Doctrine2
     * Informe les places du changement de photo
     */
    public function informPlace()
    {
        switch ($this->mustInformPlace) {
            //dans le cas d'un changement (le increase est répété ensuite)
            case self::_CHANGE_PLACE :
                $this->oldPlace->decreasePhoto();
            //dans le cas d'un ajout, seul le increase est modifié
            case self::_ADD_PHOTO :
                $this->place->increasePhoto();
                break;
            case self::_DELETE_PHOTO :
                $this->place->decreasePhoto();
            
        }
    }
    
    /**
     * Ajoute les informations adéquates dans la base de donnée
     * à partir du fichier à uploader sur le serveur
     * @return type 
     */
    public function preUpload()
    {
        if ($this->fileObjectTemp === null)
        {
            return;
        }
              
        $image = $this->photoService->toImage($this->fileObjectTemp);
        $image = $this->photoService->resizeToMaximumSize($image, self::MAXIMUM_SIZE);
        
        $this->setHeight($this->photoService->getHeight($image));
        $this->setWidth($this->photoService->getWidth($image));
        
        $this->imageTemp = $image;
        unset($this->fileObjectTemp);
    }
    
    
    
    /**
     * Cette fonction déplace et enregistre le fichier à l'emplacement
     * prévu.
     * Elle est appelée après l'enregistremetn de la photo dans la base de 
     * donnée (post-persist).
     */
    public function upload()
    {
        if ($this->imageTemp === null)
        {
            return;
        }
        
        $result = $this->photoService
                ->saveToFile(
                        $this->imageTemp, 
                        $this->getUploadRootDir().$this->file, 
                        self::COMPRESSION);
        
        
        if ($result == false)
            throw new \Exception("impossible de sauvegarder le fichier");
        
        unset($this->imageTemp);
    }

    /**
     * Set createDate
     *
     * @param datetime $createDate
     */
    private function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
    }

    /**
     * Get createDate
     *
     * @return datetime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set creator
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Management\User $creator
     */
    public function setCreator(\Progracqteur\WikipedaleBundle\Entity\Management\User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * Get creator
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Management\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set place
     *
     * @param Progracqteur\WikipedaleBundle\Entity\Model\Place $place
     */
    public function setPlace(\Progracqteur\WikipedaleBundle\Entity\Model\Place $place)
    {
        //suis le changement de place
        //dans le cas d'un changmetn de place
        if ($this->place !== null && $this->place->getId() != $place->getId())
        {
            //vérifie qu'il n'y a pas eu plusieurs changements de place
            //seule la place lors du retrait de l'instance de la BD doit être informée
            if ($this->oldPlace !== null)
                $this->oldPlace = $this->place;
            $this->mustInformPlace = self::_CHANGE_PLACE;
        } else {
            $this->mustInformPlace = self::_ADD_PHOTO;
        }
        
        $this->place = $place;
        
    }

    /**
     * Get place
     *
     * @return Progracqteur\WikipedaleBundle\Entity\Model\Place 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Photo
     */
    private function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Photo
     */
    private function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set legend
     *
     * @param string $legend
     * @return Photo
     */
    public function setLegend($legend)
    {
        $this->legend = trim($legend); //TODO: XSS protection
        return $this;
    }

    /**
     * Get legend
     *
     * @return string 
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Photo
     */
    public function setPublished($published)
    {
        if ($this->published == true && $published == false)
        {
            $this->mustInformPlace = self::_DELETE_PHOTO;
        } elseif ($this->published == false && $published == true)
        {
            $this->mustInformPlace = self::_ADD_PHOTO;
        }
        
        $this->published = $published;
        
        return $this;
        
        /*
         * FIXME : il semble que le suivi du compteur de photo ne soit pas enregistré 
         * lorsqu'une photo est dépubliée / publiée
         */
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }
    
    private function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }
    
    private function getUploadDir()
    {
        return 'uploads/images/';
    }
    
    public function getAbsolutePath()
    {
        return null === $this->file ? null : $this->getUploadRootDir().$this->file;
    }

    public function getWebPath()
    {
        return null === $this->file ? null : $this->getUploadDir().$this->file;
    }
    
    //cette partie du code sert à créer des chaines de caractères aléatoires
    //tous les caractères admins dans les chaines
    private $n = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    //longueur des chaine de caractères
    private $length = 10;

    public function createFileName() {

        $s = '';
        for ($i = 0; $i < $this->length; $i++) {

            $o = array_rand($this->n);
            $s .= $this->n[$o];
        }

        return $s;
    }
}