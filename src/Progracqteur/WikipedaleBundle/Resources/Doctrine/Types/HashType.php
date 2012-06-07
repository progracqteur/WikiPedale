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
        
        $h = new Hash();

        //si la chaine est vide, retourne le hash
        if (empty($value))
            return $h;
        
        $dom = new \DOMDocument();
         
        $dom->loadXML($value);
        
        
        
        if ($dom->hasChildNodes())
        {
            foreach ($dom->childNodes as $node)
            {
                $h->__set($node->getAttribute('key'), $node->nodeValue);
            }
        }
        
        return $h;
        
        
    }
    
    public function convertToDatabaseValue($hash, AbstractPlatform $platform)
    {
        $dom = new \DOMDocument();
        
        $ar = $hash->toArray();
        
        foreach ($ar as $key => $value) {
            $node = $dom->createElement('node', $value);
            $node->setAttribute('key', $value);
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

