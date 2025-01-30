<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException;
use \Mormat\FormulaInterpreter\Exception\UnknownFunctionException;

class FunctionCommand implements CommandInterface
{
    
    /**
     * @param string $function
     * @param CommandInterface[] $argumentCommands
     */
    public function __construct(
        protected string $function,
        protected array $argumentCommands = []
    ) {
    }
    
    public function getFunctionName()
    {
        return $this->function;
    }
    
    public function run(CommandContext $context)
    {
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
