<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

use \SplFileInfo;
use Progracqteur\WikipedaleBundle\Entity\Model\Photo;

/**
 * Description of PhotoResizer
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class PhotoService {
    
    /**
     * Renvoie un objet de la classe photo avec les dépendances requises
     * @return \Progracqteur\WikipedaleBundle\Entity\Model\Photo 
     */
    public function createPhoto()
    {
        $p = new Photo();
        $p->setPhotoService($this);
        return $p;
    }
    
    public function resizeToMaximumSize($image, $maximumSize)
    {
        
        if ($this->isLandscape($image))
        {
            return $this->resizeToWidth($image, $maximumSize);
        } else {
            return $this->resizeToHeight($image, $maximumSize);
        }
    }
    
    /**
     *
     * @param resource $image
     * @return boolean 
     */
    public function isLandscape($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($width >= $height)
            return true;
        else
            return false;
    }
    
    /**
     *
     * @param resource $image
     * @return boolean 
     */
    public function isPortrait($image)
    {
        return !$this->isLandscape($image);
    }
    
    public function toImage(SplFileInfo $file)
    {
        //TODO vérifier s'il n'y a pas d'autres types d'images possibles
        return imagecreatefromjpeg($file->getRealPath());
    }
    
    public function resizeToHeight($image, $height) {
      $ratio = $height / $this->getHeight($image);
      $width = $this->getWidth($image) * $ratio;
      return $this->resize($image, $width, $height);
   }
 
   public function resizeToWidth($image, $width) {
      $ratio = $width / $this->getWidth($image);
      $height = $this->getHeight($image) * $ratio;
      return $this->resize($image, $width, $height);
   }
 
   public function scale($image, $scale) {
      $width = $this->getWidth($image) * $scale/100;
      $height = $this->getheight($image) * $scale/100;
      return $this->resize($width,$height);
   }
 
   public function resize($image, $width, $height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $this->getWidth($image), $this->getHeight($image));
      return $new_image;
   }
   
   public function getWidth($image) {
      return imagesx($image);
   }
   
   public function getHeight($image) {
      return imagesy($image);
   }
   
   /**
    *
    * @param resource $image
    * @param string $path
    * @param int $quality
    * @return boolean 
    */
   public function saveToFile($image, $path, $quality = 98)
   {
       return \imagejpeg($image, $path, $quality);
   }
    
}

