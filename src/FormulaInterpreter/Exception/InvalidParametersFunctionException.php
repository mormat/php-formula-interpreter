<?php

namespace Mormat\FormulaInterpreter\Exception;

/**
 * Thrown if a function is called with invalid parameters
 *
 * @author mormat
 */
class InvalidParametersFunctionException extends \Exception {
    
    function __construct($functionName) {
        
        $message = sprintf(
            'Invalid parameters supplied for function "%s"',
            $functionName    
        );
        
        parent::__construct($message);
    }
    
}

