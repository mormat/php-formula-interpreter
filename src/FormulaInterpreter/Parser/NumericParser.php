<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class NumericParser implements ParserInterface  {
    
    function parse($expression) {
        
        $expression = trim($expression);
        
        if (!preg_match('/^[0-9]*(\.[0-9]*){0,1}$/', $expression)) {
            throw new ParserException($expression);
        }
        
        return $infos = array(
            'type' => 'numeric',
            'value' => floatval($expression),
        );
    }
    
}
