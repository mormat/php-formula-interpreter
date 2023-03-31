<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException;
use \Mormat\FormulaInterpreter\Exception\UnknownFunctionException;
use \Mormat\FormulaInterpreter\Functions\FunctionInterface;

/**
 * Command to execute a function
 *
 * @author mormat
 */
class FunctionCommand implements CommandInterface {
    
    /**
     * @var string
     */
    protected $function;
    
    protected $argumentCommands = array();
    
    function __construct($function, $argumentCommands = array()) {
        $this->function = $function;

        foreach ($argumentCommands as $argumentCommand) {   
            $this->addArgumentCommand($argumentCommand);
        }
    }
    
    public function getFunctionName()
    {
        return $this->function;
    }
    
    public function addArgumentCommand(CommandInterface $argumentCommand)
    {
        $this->argumentCommands[] = $argumentCommand;
    }

    public function run(CommandContext $context) {
        
        if (!$context->hasFunction($this->function)) {
            throw new UnknownFunctionException($this->function);
        }
        
        $function = $context->getFunction($this->function);
        
        $arguments = array();
        foreach ($this->argumentCommands as $command) {
            $arguments[] = $command->run($context);
        }
        
        if (!$function->supports($arguments)) {
            throw new InvalidParametersFunctionException(sprintf(
                "Invalid parameters provided to function '%s'",
                $function->getName()
            ));
        }
        
        return $function->execute($arguments);
    }
}
