<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mormat
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
