<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;

use Doctrine\DBAL\Types\Type; 
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Progracqteur\WikipedaleBundle\Resources\Container\Address;

/**
 * Description of AddressType
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class AddressType extends Type {
    
    const ADDRESS = 'address';
    
    public function getName() {
        return self::ADDRESS;
    }
    
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'xml';
    }
    
    public function convertToDatabaseValue($address, AbstractPlatform $platform )
    {
        
        
         $dom = new \DOMDocument('1.0', 'utf-8');
         $parent = $dom->createElement('addressparts');
         $dom->appendChild($parent);
        
        $ar = $address->toArray();
        
        foreach ($ar as $key => $value) {
            $node = $dom->createElement($key, $this->parseString($value));
            
            $parent->appendChild($node);
        }
        $s = $dom->saveXML();
        
        
        return $s;
    }
    
    private function parseString($string){
        
        $pattern = "/'/i";
        $replacement = "\'";
        
        return preg_replace($pattern, $replacement, $string);
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $a = new Address();

        //si la chaine est vide, retourne le hash
        if (empty($value))
            return $a;
        
        $dom = new \DOMDocument('1.0', 'utf-8');
         
        $dom->loadXML($value);
        $docs = $dom->getElementsByTagName('addressparts');
        
        $doc = $docs->item(0);

        if ($dom->hasChildNodes())
        {
            foreach ($doc->childNodes as $node)
            {
                $v = $node->nodeValue;
                
                switch ($node->nodeName) {
                    case Address::CITY_DECLARATION :
                        $a->setCity($v);
                        break;
                    case Address::ADMINISTRATIVE_DECLARATION :
                        $a->setAdministrative($v);
                        break;
                    case Address::COUNTY_DECLARATION :
                        $a->setCounty($v);
                        break;
                    case Address::STATE_DISTRICT_DECLARATION :
                        $a->setStateDistrict($v);
                        break;
                    case Address::STATE_DECLARATION :
                        $a->setState($v);
                        break;
                    case Address::COUNTRY_DECLARATION :
                        $a->setCountry($v);
                        break;
                    case Address::COUNTRY_CODE_DECLARATION :
                        $a->setCountryCode($v);
                        break;
                }
            }
        }
        
        return $a;
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

