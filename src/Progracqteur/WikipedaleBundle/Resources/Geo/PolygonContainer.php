<?php

namespace Progracqteur\WikipedaleBundle\Resources\Geo;



/**
 * Description of Point
 *
 * @author user
 */
class Polygon {
    
    private $stringPostgis = '';
    
    private function __construct($string) {
        $this->stringPostgis = $string;
    }
    
    public function __toString() {
        return $this->stringPostgis;
    }
    
    
}


