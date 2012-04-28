<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

/**
 * Description of HashException
 *
 * @author user
 */
class HashException extends \Exception {
    
    public static function HashNotExist($name)
    {
        return new self("The requested index $name does not exist in this Hash");
    }
}

