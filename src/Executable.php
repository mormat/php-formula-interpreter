<?php

namespace Mormat\FormulaInterpreter;

/**
 * Description of Compiler
 *
 * @author mormat
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
        
        $context = new Command\CommandContext($variables);
        return $this->command->run($context);
    }
    
}
