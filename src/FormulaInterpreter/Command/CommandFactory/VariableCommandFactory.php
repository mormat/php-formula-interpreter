<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\VariableCommand;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class VariableCommandFactory implements CommandFactoryInterface  {
    
    protected $variables;
    
    function __construct($variables) {
        $this->variables = $variables;
    }
    
    public function create($options) {
        if (!isset($options['name'])) {
            throw new CommandFactoryException();
        }
        
        return new VariableCommand(
                $options['name'],
                $this->variables
        );        
    }
    
}
