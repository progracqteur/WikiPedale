<?php

namespace Progracqteur\WikipedaleBundle\Resources\Geo;



/**
 * Description of Point
 *
 * @author user
 */
class Polygon {
    
    private $vertex = array();
    
    public static $SRID = '4326';
    
    private function __construct($vertexes = array()) {
        foreach ($vertexes as $key => $values)
        {
            $this->vertex[] = array('lon' => $values['lon'], 'lat' => $values['lat']);
        }
    }
    
    public function toGeoJson(){
        $array = $this->toArrayGeoJson();
        return \json_encode($array);
    }
    
    public function toArrayGeoJson()
    {
        $rings = array();
        $ring = array();
        foreach ($this->vertex as $point)
        {
            $ring[] = array($point['long'], $point['lat']);
        }
        
        $rings[] = $ring;
        return array("type" => "Polygon", "coordinates" => $rings);
    }
    
    /**
     *
     * @return string 
     */
    public function toWKT() {
        return '';
        //to be implemented
        $coordinates = '';
        
        
        
        return 'SRID='.self::$SRID.';POINT('.$this->lon.' '.$this->lat.')';
    }
    
    /**
     *
     * @param type $geojson
     * @return Point 
     */
    public static function fromGeoJson($geojson) 
    {
        return self::_contruct();
                
    }
    
    public static function fromLonLat($lon, $lat)
    {
        return self::_construct();
    }
    
    public static function fromArrayGeoJson($array)
    {
        return self::_construct();
    }
    
    
}


