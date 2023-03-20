<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse numeric values in formulas
 *
 * @author mormat
 */
class NumericParser implements ParserInterface  {
    
    function parse($rawExpression) {
        
        $expression = trim($rawExpression);
        
        if (!preg_match('/^[0-9]*(\.[0-9]*){0,1}$/', $expression)) {
            throw new ParserException($rawExpression);
        }
        
        return $infos = array(
            'type' => 'numeric',
            'value' => floatval($expression),
        );
    }
    
}
