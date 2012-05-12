<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;
 
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
 
class FileType extends Type
{
    const FILE = 'file';
 
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        //return $platform->getDoctrineTypeMapping('BLOB');
        return 'text';
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
        return self::FILE;
    }
}