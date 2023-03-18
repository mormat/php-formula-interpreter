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

?>
