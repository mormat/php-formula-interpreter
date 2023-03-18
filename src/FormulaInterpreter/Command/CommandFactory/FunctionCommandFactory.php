<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\FunctionCommand;

/**
 * Description of FunctionParser
 *
 * @author mathieu
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
    
    public function registerFunction($name, $callable) {
        $this->functions[$name] = $callable;
    }
    
    public function create($options) {
        
        if (!isset($options['name'])) {
            throw new CommandFactoryException('Missing option "name"');
        }
        
        if (!isset($this->functions[$options['name']])) {
            throw new \FormulaInterpreter\Exception\UnknownFunctionException($options['name']);
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

?>
