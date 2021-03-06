<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

use Progracqteur\WikipedaleBundle\Resources\Container\HashException;

/**
 * Description of Hash
 *
 * @author user
 */
class Hash {
    private $container = array();
    
    public function __get($name) 
    {
        if (isset($this->container[$name]))
                return $this->container[$name];
        else
            throw HashException::indexNotExist($name);
    }
    
    public function __set($name, $value)
    {
        $this->container[$name] = $value;
    }
    
    public function __string()
    {
        return json_encode($this->container);
    }
    
    public function toArray(){
        return $this->container;
    }
    
    public function has($name)
    {
        if (isset($this->container[$name]))
            return true;
        else
            return false;
    }
    
    public function remove($name)
    {
        unset($this->container[$name]);
    }
    
    public function equals(Hash $hash)
    {
        if ($hash->toArray() == $this->toArray())
        {
            return true;
        } else 
        {
            return false;
        }
        
    }
    
}

