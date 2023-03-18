<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\NotEnoughArgumentsException;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class FunctionCommand implements CommandInterface {
    
    protected $callable;
    
    protected $argumentCommands = array();
    
    function __construct($callable, $argumentCommands = array()) {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException();        
        }
        
        $this->callable = $callable;

        foreach ($argumentCommands as $argumentCommand) {   
            if (!($argumentCommand instanceof CommandInterface)) {
                throw new \InvalidArgumentException();        
            }
        }
        
        $reflection = new \ReflectionFunction($this->callable);
        if (sizeof($argumentCommands) < $reflection->getNumberOfRequiredParameters()) {
            throw new NotEnoughArgumentsException();
        }
        
        $this->argumentCommands = $argumentCommands;
    }

    public function run() {
        $arguments = array();
        foreach ($this->argumentCommands as $command) {
            $arguments[] = $command->run();
        }
        
        return call_user_func_array($this->callable, $arguments);
    }
}
