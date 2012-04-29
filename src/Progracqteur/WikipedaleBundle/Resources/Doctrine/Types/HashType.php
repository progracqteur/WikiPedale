<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;

use Doctrine\DBAL\Types\Type; 
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Progracqteur\WikipedaleBundle\Resources\Container\Hash;

/**
 * A Type for Doctrine to implement the hstore capabilities
 * from postgresql databases
 *
 * @author Julien Fastre <julien AT fastre POINT info>
 */
class HashType extends Type {
    
    const HASH = 'hash';
    
    public function getName()
    {
        return self::HASH;
    }
    
    /**
     *
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return type 
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'xml';
    }
    
    /**
     *
     * @param Hash $value
     * @param AbstractPlatform $platform
     * @return Progracqteur\WikipedaleBundle\Resources\Container\Hash 
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($value);
        
        $h = new Hash();
        
        if ($dom->hasChildNodes())
        {
            foreach ($dom->childNodes as $node)
            {
                $h->__set($node->nodeName, $node->nodeValue);
            }
        }
        
        return $h;
        
        
    }
    
    public function convertToDatabaseValue($hash, AbstractPlatform $platform)
    {
        $dom = new \DOMDocument();
        
        foreach ($hash as $key => $value) {
            $node = $dom->createElement($key, $value);
            $dom->appendChild($node);
        }
        return $dom->saveXML();
        
        /*$str = 'hstore(ARRAY['; 
        $i = 0;
        foreach ($hash as $key => $value)
        {
            if ($i > 0) {
                $str .= ',';
            }
            $str .= '[\''.$key.'\',\''.$value.'\']';
        }
        
        $str.='])';
        return $str;*/
        
    }
    
    
}

