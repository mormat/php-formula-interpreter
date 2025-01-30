<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\UnknownVariableException;

class VariableCommand implements CommandInterface
{
    public function __construct(protected string $name)
    {
        if (!$name) {
            throw new \TypeError("Variable name '$name' should not be empty");
        }
        if (is_numeric($name)) {
            throw new \TypeError("Variable name '$name' should not be numeric");
        }
    }
    
    public function getVariableName()
    {
        return $this->name;
    }

    public function run(CommandContext $context)
    {
        if (!$context->hasVariable($this->name)) {
            throw new UnknownVariableException($this->name);
        }
        
        return $context->getVariable($this->name);
    }
}
