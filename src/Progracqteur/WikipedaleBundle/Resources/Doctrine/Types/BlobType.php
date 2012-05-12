<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;
 
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
 
class BlobType extends Type
{
    const BLOB = 'blob';
 
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDoctrineTypeMapping('BLOB');
    }
 
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return ($value === null) ? null : base64_encode($value);
    }
 
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return ($value === null) ? null : base64_decode($value);
    }
 
    public function getName()
    {
        return self::BLOB;
    }
}