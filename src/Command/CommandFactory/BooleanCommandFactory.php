<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\BooleanCommand;

class BooleanCommandFactory implements CommandFactoryInterface {
    
    public function create($options): CommandInterface {
        if (!isset($options['value'])) {
            throw new CommandFactoryException();
        }
        
        return new BooleanCommand($options['value']);
    }
    
}
