<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\NumericCommand;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class NumericCommandFactory implements CommandFactoryInterface {
    
    public function create($options) {
        if (!isset($options['value'])) {
            throw new CommandFactoryException();
        }
        
        return new NumericCommand($options['value']);
        
    }
    
}