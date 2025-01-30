<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\OperationCommand;

class OperationCommandFactory implements CommandFactoryInterface
{
    
    public function __construct(
        protected CommandFactoryInterface $childCommandFactory
    ) {
    }
    
    public function create($options): CommandInterface
    {
        $left  = $this->childCommandFactory->create($options['left']);
        $right = $this->childCommandFactory->create($options['right']);
        
        return new OperationCommand($left, $options['operator'], $right);
    }
}
