<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

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
    
    
    public function __construct($results)
    {
        $this->setResults($results);
    }
    
    public function setResults($results)
    {
        $this->results = $results;
        foreach ($results as $object)
        {
            $this->count++;
        }
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

