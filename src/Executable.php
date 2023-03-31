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
     * @var FunctionInterface[]
     */
    protected $functions;
    
    function __construct(Command\CommandInterface $command, $functions = []) {
        $this->command   = $command;
        $this->functions = $functions;
    }

    function run($variables = array()) {        
        $context = new Command\CommandContext($variables, $this->functions);
        return $this->command->run($context);
    }
    
}
