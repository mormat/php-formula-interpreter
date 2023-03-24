<?php

namespace Mormat\FormulaInterpreter\Command\CommandFactory;

use \Mormat\FormulaInterpreter\Command\FunctionCommand;
use \Mormat\FormulaInterpreter\Exception\UnknownFunctionException;
use \Mormat\FormulaInterpreter\Functions\FunctionInterface;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class FunctionCommandFactory implements CommandFactoryInterface {
    
    protected $functions = array();
    
    /**
     * @var CommandFactoryInstance
     */
    protected $argumentCommandFactory;
    
    function __construct(CommandFactoryInterface $argumentCommandFactory) {
        $this->argumentCommandFactory = $argumentCommandFactory;
    }
    
    public function registerFunction(FunctionInterface $function) {
        $this->functions[$function->getName()] = $function;
    }
    
    public function getFunctions() {
        return $this->functions;
    }
   
    public function create($options) {
        
        if (!isset($options['name'])) {
            throw new CommandFactoryException('Missing option "name"');
        }
        
        if (!isset($this->functions[$options['name']])) {
            throw new UnknownFunctionException($options['name']);
        }
        
        $argumentCommands = array();
        if (isset($options['arguments'])) {
            foreach ($options['arguments'] as $argumentOptions) {
                $argumentCommands[] = $this->argumentCommandFactory->create($argumentOptions);
            }
        }
        
        return new FunctionCommand($this->functions[$options['name']], $argumentCommands);
    }
    
}
