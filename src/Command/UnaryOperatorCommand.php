<?php

namespace Mormat\FormulaInterpreter\Command;

use Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

class UnaryOperatorCommand implements CommandInterface
{
    public function __construct(
        protected string $operator,
        protected CommandInterface $childCommand
    ) {
    }
    
    public function run(CommandContext $context)
    {
        $value = $this->childCommand->run($context);
        
        if ($this->operator === 'not') {
            return !$value;
        }

        if ($this->operator === '-') {
            if (!is_numeric($value) && !is_bool($value)) {
                throw new UnsupportedOperandTypeException(
                    'Unsupported operand type in - operation'
                );
            }
            return -$value;
        }
    }
}
