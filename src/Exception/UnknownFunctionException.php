<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Thrown if formula contains unknow function
 *
 * @author mormat
 */
class UnknownFunctionException extends BaseException
{
    
    public function __construct(protected string $name)
    {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown function "%s"', $name));
    }
    
    /**
     * get the function name that triggers the exception
     * @todo rename to getFunctionName
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
