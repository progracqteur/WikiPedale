<?php

namespace Progracqteur\WikipedaleBundle\Resources\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * Description of Crosses
 *
 * @author Julien Fastré <julien arobase fastre point info>
 */
class Crosses extends FunctionNode {
    
    /**
     * contains the string representing the geography in the postgis encoding
     * @var  
     */
    private $geogNativeStringA;
    
    /**
     * contains the string representing the geography in the postgis encoding
     * @var 
     */
    private $geogNativeStringB;

    
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return "ST_Intersects(GeomFromEWKT(ST_AsEWKT(".
                $this->geogNativeStringA->dispatch($sqlWalker).
                ")), GeomFromEWKT(ST_AsEWKT(".
                $this->geogNativeStringB->dispatch($sqlWalker).
                ")))";
        //FIXME : function ST_Intersects should be replaced by ST_Crosses, as postgis's documentation
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        
        $this->geogNativeStringA = $parser->StringExpression();
        
        $parser->match(Lexer::T_COMMA);
        
        $this->geogNativeStringB = $parser->StringExpression();
        
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);  
    }
}

