<?php

namespace Progracqteur\WikipedaleBundle\Resources\Geo;



/**
 * Description of Point
 *
 * @author user
 */
class Point {
    private $lat;
    private $lon;
    public static $SRID = '4326';
    
    private function __construct($lon, $lat) {
        $this->lat = $lat;
        $this->lon = $lon;
    }
    
    public function toGeoJson(){
        $array = $this->toArrayGeoJson();
        return \json_encode($array);
    }
    
    public function toArrayGeoJson()
    {
        return array("type" => "Point", "coordinates" => array ($this->lon, $this->lat));
    }
    
    /**
     *
     * @return string 
     */
    public function toWKT() {
        return 'SRID='.self::$SRID.';POINT('.$this->lon.' '.$this->lat.')';
    }
    
    /**
     *
     * @param type $geojson
     * @return Point 
     */
    public static function fromGeoJson($geojson) 
    {
        $a = json_decode($geojson);
        //check if the geojson string is correct
        if ($a == null or !isset($a->type) or !isset($a->coordinates)){
            throw PointException::badJsonString($geojson);
        }
        
        if ($a->type != "Point"){
            throw PointException::badGeoType();
        } else {
            $lat = $a->coordinates[0];
            $lon = $a->coordinates[1];
            return Point::fromLonLat($lon, $lat);
        }
                
    }
    
    public static function fromLonLat($lon, $lat)
    {
        if (($lon > -180 && $lon < 180) && ($lat > -90 && $lat < 90))
        {
            return new Point($lon, $lat);
        } else {
            throw PointException::badCoordinates($lon, $lat);
        }
    }
    
    public function getLat()
    {
        return $this->lat;
    }
    
    public function getLon()
    {
        return $this->lon;
    }
}


