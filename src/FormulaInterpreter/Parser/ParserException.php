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
class ParserException extends \Exception {

    /**
     * @var string
     */
    protected $expression;
    
    function __construct($expression) {
        $this->expression = $expression;
        
        $message = sprintf("Failed to parse expression '%s'", $expression);
        parent::__construct($message);
    }

    public function getExpression() {
        return $this->expression;
    }
    
}

?>
