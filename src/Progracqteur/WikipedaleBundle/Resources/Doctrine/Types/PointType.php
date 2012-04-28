<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;

use Progracqteur\WikipedaleBundle\Resources\Geo\Point;
use Doctrine\DBAL\Types\Type; 
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Progracqteur\WikipedaleBundle\Resources\Geo\PointException;




/**
 * A Type for Doctrine to implement the Geography Point type
 * implemented by Postgis on postgis+postgresql databases
 *
 * @author user
 */
class PointType extends Type {
    
    const POINT = 'point';
    
    /**
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return type 
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'geography(POINT,'.Point::$SRID.')';
    }
    
    /**
     *
     * @param type $value
     * @param AbstractPlatform $platform
     * @return Point 
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return Point::fromGeoJson($value);
    }
    
    public function getName()
    {
        return self::POINT;
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->toWKT();
    }
    
    public function canRequireSQLConversion()
    {
        return true;
    }
    
    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
        return 'ST_AsGeoJSON('.$sqlExpr.') ';
    }
    
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return $sqlExpr;
    }
    
    
    
    
    
}

