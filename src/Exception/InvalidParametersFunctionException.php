<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Thrown if a function is called with invalid parameters
 *
 * @author mormat
 */
class InvalidParametersFunctionException extends BaseException
{
    public function __construct($functionName)
    {
        $message = sprintf(
            'Invalid parameters supplied for function "%s"',
            $functionName
        );
        
        parent::__construct($message);
    }
}
