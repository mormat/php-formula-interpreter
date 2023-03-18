<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter;

/**
 * Description of Compiler
 *
 * @author mathieu
 */
class Executable {
    
    /**
     * @var Command\CommandInterface
     */
    protected $command;
    
    /**
     * @var \ArrayObject
     */
    protected $variables;
    
    function __construct(Command\CommandInterface $command, \ArrayObject $variables) {
        $this->command = $command;
        $this->variables = $variables;
    }

    function run($variables = array()) {
        $this->variables->exchangeArray($variables);
        return $this->command->run();
    }
    
}

?>
