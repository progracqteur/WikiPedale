<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Types;

use Doctrine\DBAL\Types\Type; 
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * A Type for Doctrine to implement the hstore capabilities
 * from postgresql databases
 *
 * @author Julien Fastre <julien AT fastre POINT info>
 */
class HashType extends Type {
    
    cont HASH = 'hash';
    
    public function getName(){
        return self::HASH;
    }
    
    
}

