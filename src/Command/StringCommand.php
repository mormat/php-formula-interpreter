<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Execute of a string expression
 */
class StringCommand implements CommandInterface
{
    public function __construct(protected $value)
    {
        if (!is_string($value)) {
            $message = sprintf(
                'Parameter $value of method __construct() of class %s must be a string. Got %s type instead.',
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
