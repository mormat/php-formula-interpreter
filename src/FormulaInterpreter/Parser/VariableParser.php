<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class VariableParser implements ParserInterface{
    
    function parse($expression) {
        $expression = trim($expression);
        
        if (!preg_match('/^([a-zA-Z_]+[0-9]*)+$/', $expression)) {
            throw new ParserException($expression);
        }
        
        return array(
            'type' => 'variable',
            'name' => $expression,
        );
    }
    
}

?>
