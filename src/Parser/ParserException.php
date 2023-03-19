<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class ParserException extends BaseException {

    /**
     * @var string
     */
    protected $expression;
    
    function __construct($expression) {
        $this->expression = $expression;
        
        $message = sprintf('Failed to parse expression %s', $expression);
        parent::__construct($message);
    }

    public function getExpression() {
        return $this->expression;
    }
    
}
