<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 * Description of NormalizedResponse
 *
 * @author julien
 */
class NormalizedResponse {
    
    private $results;
    private $total;
    private $count = 0;
    private $start = 0;
    private $limit;
    private $user = null;
    
    
    public function __construct($results = null)
    {
        if ($results !== null)
            $this->setResults($results);
    }
    
    public function setResults($results)
    {
        $this->results = $results;
        if (is_array($results)) 
        {
            foreach ($results as $object)
            {
                $this->count++;
            }
        } else {
            $this->count = 1;
        }
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    public function hasUser()
    {
        if ($this->user === null) {
            return false;
        } else 
        {
            return true;
        }
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setTotal($total)
    {
        $this->total = $total;
    }
    
    
    public function setStart($start)
    {
        $this->start = $start;
    }
    
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    public function getLimit()
    {
        return $this->limit;
    }
    
    public function getStart()
    {
        return $this->start;
    }
    
    public function getTotal()
    {
        return $this->total;
    }
    
    public function getCount()
    {
        return $this->count;
    }
    
    public function getResults()
    {
        return $this->results;
    }
}

