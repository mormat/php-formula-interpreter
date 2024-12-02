<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Parser\ParserException;

class ExpressionCleanerDecorator implements ParserInterface {
    
    public function __construct(
        protected ParserInterface $base
    ) { }

    public function getBase(): ParserInterface {
        return $this->base;
    }
 
    public function parse($expression): array {
        
        $cleaned = trim($expression);
        
        if (str_starts_with($cleaned, '(') && str_ends_with($cleaned, ')')) {
            $betweenParenthesis = trim(substr($cleaned, 1, -1));
            if ($this->isParenthesisWellFormed($betweenParenthesis)) {
                $cleaned = $betweenParenthesis;
            }
        }
        
        if ($cleaned === "") {
            throw new ParserException($expression);
        }
        
        return $this->base->parse($cleaned);
    }
    
    function isParenthesisWellFormed($expr): bool {
        $countOpenedParenthesis = 0;
        for ($i = 0; $i < strlen($expr); $i++) {
            if ($expr[$i] === '(') {
                $countOpenedParenthesis++;
            }
            if ($expr[$i] === ')') {
                $countOpenedParenthesis--;
            }
            if ($countOpenedParenthesis < 0) {
                return false;
            }
        }
        
        
        return $countOpenedParenthesis === 0;
    }
    
}
