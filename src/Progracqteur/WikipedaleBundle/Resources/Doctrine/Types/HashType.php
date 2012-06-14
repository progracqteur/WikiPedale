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
        $parent = $dom->createElement('parent');
        $dom->appendChild($parent);
        
        $ar = $this->parseToArray($hash);
        
        foreach ($ar as $key => $value) {
            $e = $this->transformRecursiveToDom($dom, $key, $value);
            $dom->appendChild($e);
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
    
    private function parseToArray(Hash $hash)
    {
        $ar = $hash->toArray();
        foreach ($ar as $key => $value)
        {
            if ($value instanceof Hash) 
            {
                $ar[$key] = $this->parseToArray($value);
            }
        }
        
        return $ar;
    }
    
    private function transformRecursiveToDom(\DOMDocument $dom, $key, $data)
    {
        if (is_array($data))
        {
            $tree = $dom->createElement('tree');
            foreach ($data as $key => $value) 
            {
                $a = $this->transformRecursiveToDom($dom, $value);
                $tree->appendChild($a);
            }
            return $tree;
        } else {
            
            if ($data instanceof \DateTime)
            {
                $data = $data->format('u');
            }
            
            $node = $dom->createElement('node', $data);
            $node->setAttribute('key', $key);
            return $node;
        }
    }
    
    
}

