<?php

namespace Progracqteur\WikipedaleBundle\Resources\Container;

/**
 * Description of NormalizedErrorResponse
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class NormalizedExceptionResponse {
    
    private $exception;
    
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }
    
    public function getException()
    {
        return $this->exception;
    }
}

