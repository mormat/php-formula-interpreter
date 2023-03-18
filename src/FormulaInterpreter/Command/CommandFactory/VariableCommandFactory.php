<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\VariableCommand;

/**
 * Description of FunctionParser
 *
 * @author mathieu
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

?>
