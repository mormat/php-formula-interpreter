<?php

namespace Mormat\FormulaInterpreter\Command;

class NumericCommand implements CommandInterface
{
    public function __construct(protected $value)
    {
        if (!is_numeric($value)) {
            $message = sprintf(
                'Parameter $value of method __construct() of class %s must be an integer. Got %s type instead.',
                get_class($this),
                gettype($value)
            );
            throw new \TypeError($message);
        }
    }
    
    public function run(CommandContext $context)
    {
        return $this->value;
    }
}
