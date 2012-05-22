<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Description of Covers
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class Covers extends FunctionNode {
    
    private $bbox = null;
    private $point = null;
    
    
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return 'ST_Covers(ST_GeogFromText('.$this->bbox->dispatch($sqlWalker).'), '.$this->point->dispatch($sqlWalker).')';
    }
    
    public function parse(\Doctrine\ORM\Query\Parser $parser) {
                
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->bbox = $parser->StringExpression();
        
        $parser->match(Lexer::T_COMMA);
        
        $this->point = $parser->StringPrimary();
        
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
        
    }
}

