<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\CommandInterface;
use \Mormat\FormulaInterpreter\Command\StringCommand;

/**
 * Creates a command to execute a string expression
 *
 * @author mormat
 */
class StringCommandFactory implements CommandFactoryInterface
{
    public function create($options): CommandInterface
    {
        if (!isset($options['value'])) {
            throw new CommandFactoryException();
        }
        
        return new StringCommand($options['value']);
    }
}
