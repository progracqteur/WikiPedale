<?php

namespace Progracqteur\WikipedaleBundle\Resources\Services;

/**
 * Description of SlugService
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class SlugService {
    
    /**
     * 
     * @param string $string
     * @return string slug form of the string
     */
    public function slug($string)
    {
        $string = trim($string);
        $string = strtolower($string);
        
        


        
        $a = array('À','Á','Â','Ã','Ä','Å','Æ', 'Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ', 'ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Œ', 'œ', 'Š','š','Ÿ','Ž','ž','ƒ', ' ');
        $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','OE','oe','S','s','Y','z','Z','f', '-');
        $string = str_replace($a, $b, $string);
        
        // replace non letter or digits by -
        $string = preg_replace('/\'/', '', $string);
        
        return $string;
        
    
    }
    
}

