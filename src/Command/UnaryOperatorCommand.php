<?php

namespace Mormat\FormulaInterpreter\Command;

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
    }
}
