<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

class UnknownVariableException extends BaseException
{
    
    public function __construct(protected string $name)
    {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown variable "%s"', $name));
    }
    
    /**
     * get the variable name
     * @todo rename to getVariableName()
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
