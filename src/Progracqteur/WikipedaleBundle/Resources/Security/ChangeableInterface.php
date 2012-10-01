<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security;

/**
 *
 * @author julien arobase fastre.info
 */
interface ChangeableInterface {
    
    /**
     * @return Progracqteur\WikipedaleBundle\Resources\Security\ChangesetsInterface
     */
    public function getChangeset();
}

