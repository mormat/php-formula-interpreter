<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException;
use \Mormat\FormulaInterpreter\Exception\NotEnoughArgumentsException;
use \Mormat\FormulaInterpreter\Functions\FunctionInterface;


/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class FunctionCommand implements CommandInterface {
    
    protected $function;
    
    protected $argumentCommands = array();
    
    function __construct(FunctionInterface $function, $argumentCommands = array()) {
        $this->function = $function;

        foreach ($argumentCommands as $argumentCommand) {   
            if (!($argumentCommand instanceof CommandInterface)) {
                throw new \InvalidArgumentException();        
            }
        }
        
        
        $this->argumentCommands = $argumentCommands;
    }

    public function run() {
        $arguments = array();
        foreach ($this->argumentCommands as $command) {
            $arguments[] = $command->run();
        }
        
        if (!$this->function->supports($arguments)) {
            throw new InvalidParametersFunctionException(sprintf(
                "Invalid parameters provided to function '%s'",
                $this->function->getName()
            ));
        }
        
        return $this->function->execute($arguments);
    }
}
