<?php

namespace Mormat\FormulaInterpreter\Parser;

class LeadingWhitespaceParser implements ParserInterface {

    public function __construct(
        protected ParserInterface $childParser
    ) { }

    public function parse($expression) {
        
        if ($expression !== '') {
            if ($expression[0] === ' ' || $expression[-1] === ' ') {
                return $this->childParser->parse(trim($expression));
            }
        }
        
        throw new ParserException($expression);
        
    }
    
}
