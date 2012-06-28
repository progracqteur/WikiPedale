<?php

namespace Progracqteur\WikipedaleBundle\Resources\Security;


use \Iterator;
use Progracqteur\WikipedaleBundle\Entity\Management\User;

/**
 *
 * @author julien arobase fastre.info
 */
interface ChangesetInterface extends Iterator {
    
    public function getAuthor();
    
    public function setAuthor(User $user);
    
    public function isCreation();
}


