<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;

use Progracqteur\WikipedaleBundle\Resources\Geo\Polygon;
use Doctrine\DBAL\Types\Type; 
use Doctrine\DBAL\Platforms\AbstractPlatform;




/**
 * A Type for Doctrine to implement the Geography Point type
 * implemented by Postgis on postgis+postgresql databases
 *
 * @author user
 */
class PolygonType extends Type {
    
    const NAME = 'polygon';
    
    /**
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return type 
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'geography(POLYGON,'.Polygon::$SRID.')';
    }
    
    /**
     *
     * @param type $value
     * @param AbstractPlatform $platform
     * @return Point 
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value; //Polygon::fromGeoJson($value);
    }
    
    public function getName()
    {
        return self::NAME;
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }
    
    public function canRequireSQLConversion()
    {
        return false;
    }
    

    
    
    
    
    
}

