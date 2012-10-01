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
        
        $parent = $dom->getElementsByTagName('parent');
        
        $parent = $parent->item(0);
        
        if ($parent->hasChildNodes())
        {
            foreach ($parent->childNodes as $node)
            {
                $h->__set($node->getAttribute('key'), $this->transformXMLtoHash($dom, $node));
            }
        }
        
        return $h;
        
        
    }
    
    private function transformXMLtoHash(\DOMDocument $dom, \DOMNode $node)
    {
        if ($node->tagName == 'tree')
        {
            
            $h = new Hash();
            
            {
                foreach ($node->childNodes as $n)
                {
                    $h->__set($n->getAttribute('key'), $this->xmlentities_backtostring($this->transformXMLtoHash($dom, $n)));
                }
            }
            return $h;
        } else {
            return $node->nodeValue;
        }
    }
    
    public function convertToDatabaseValue($hash, AbstractPlatform $platform)
    {
        $dom = new \DOMDocument();
        $parent = $dom->createElement('parent');
        $dom->appendChild($parent);
        
        $ar = $this->parseToArray($hash);
        
        foreach ($ar as $key => $value) {
            $e = $this->transformRecursiveToDom($dom, $key, $value);
            $parent->appendChild($e);
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
            $tree->setAttribute('key', $key);
            
            foreach ($data as $key => $value) 
            {
                $a = $this->transformRecursiveToDom($dom, $key, $value);
                $tree->appendChild($a);
            }
            return $tree;
        } else {
            
            $node = $dom->createElement('node');
            
            if ($data instanceof \DateTime)
            {
                $data = '@'.$data->format('U');
            }
            
            
            $text = $dom->createTextNode($this->xmlentities($data));
            $node->appendChild($text);
            $node->setAttribute('key', $key);
            return $node;
        }
    }
    
    private function xmlentities($string) {
        return str_replace(array("&", "<", ">", "\"", "'"),
            array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $string);
    }
    
    private function xmlentities_backtostring($string)
    {
        return str_replace(array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"),
                array("&", "<", ">", "\"", "'"), $string);
    }

    
        public function canRequireSQLConversion()
    {
        return true;
    }

    public function convertToPHPValueSQL($sqlExpr, $platform)
    {
       return 'XMLSERIALIZE(DOCUMENT '.$sqlExpr.' AS text )';
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return 'XMLPARSE (DOCUMENT '.$sqlExpr.")";
    }
    
    
}

