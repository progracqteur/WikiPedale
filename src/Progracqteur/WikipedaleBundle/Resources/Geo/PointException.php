<?php
namespace Progracqteur\WikipedaleBundle\Resources\Geo;
use \Exception;

/**
 * Description of PointException
 *
 * @author user
 */
class PointException extends Exception {

    public static function badCoordinates($lon, $lat) 
    {
        return new self("les coordonnées fournies sont invalides dans le système de projection utilisé (longitude = $lon , latitude = $lat)");
    }

    public static function badJsonString($str) 
    {
        return new self("la chaine JSON n'est pas valide : $str");
        
    }
    
    public static function badGeoType()
    {
        return new self("le type de l'objet GeoJSON est invalide");
    }
}

