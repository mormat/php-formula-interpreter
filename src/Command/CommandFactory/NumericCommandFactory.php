<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\CommandInterface;
use \Mormat\FormulaInterpreter\Command\NumericCommand;

class NumericCommandFactory implements CommandFactoryInterface
{
    public function create($options): CommandInterface
    {
        if (!isset($options['value'])) {
            throw new CommandFactoryException();
        }
        
        return new NumericCommand($options['value']);
    }
}
