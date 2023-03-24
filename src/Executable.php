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
    
    function __construct(Command\CommandInterface $command) {
        $this->command = $command;
    }

    function run($variables = array()) {        
        $context = new Command\CommandContext($variables);
        return $this->command->run($context);
    }
    
}
