<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\CommandInterface;
use \Mormat\FormulaInterpreter\Command\VariableCommand;

class VariableCommandFactory implements CommandFactoryInterface
{
    public function create($options): CommandInterface
    {
        if (!isset($options['name'])) {
            throw new CommandFactoryException();
        }
        
        return new VariableCommand(
            $options['name']
        );
    }
}
