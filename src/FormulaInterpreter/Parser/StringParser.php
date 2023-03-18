<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse strings in formulas
 *
 * @author mormat
 */
class StringParser implements ParserInterface  {
    
    public function parse($expression) {
        
        $expression = trim($expression);
        
        if ($expression[0] !== "'" || $expression[-1] !== "'") {
            throw new ParserException($expression);
        }
        
        $value = substr($expression, 1, -1);
        
        if (strpos($value, "'") !== false) {
            throw new ParserException($expression);
        }
        
        return array(
            'type'  => 'string',
            'value' => $value,
        );
        
    }

}