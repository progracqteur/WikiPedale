<?php

namespace Progracqteur\WikipedaleBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Check if the field is valid. 
 * 
 * The validation is done against parameters.place_types, defined in 
 * parameters.yml (see parameters.yml.dist for an example)
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class TermConstraint extends Constraint {
    
    public function validatedBy() {
        return 'term';
    }
    
    public function getTargets() {
        return self::CLASS_CONSTRAINT;
    }
    
}

