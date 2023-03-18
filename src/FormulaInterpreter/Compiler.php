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
class Compiler {
    
    /**
     * @var Parser\CompositeParser
     */
    protected $parser;
    
    /**
     * @var Command\CommandFactory;
     */
    protected $commandFactory;
    
    /**
     * @var \ArrayObject
     */
    protected $variables;
    
    function __construct() {
        
        $this->parser = new Parser\CompositeParser();
        $this->parser->addParser(new Parser\NumericParser());
        $this->parser->addParser(new Parser\VariableParser());
        $this->parser->addParser(new Parser\FunctionParser($this->parser));
        $this->parser->addParser(new Parser\OperatorParser($this->parser));
        
        $this->variables = new \ArrayObject();
        
        $this->commandFactory = new Command\CommandFactory();
        $this->commandFactory->registerFactory('numeric', new Command\CommandFactory\NumericCommandFactory());
        $this->commandFactory->registerFactory('variable', new Command\CommandFactory\VariableCommandFactory($this->variables));
        $this->commandFactory->registerFactory('operation', new Command\CommandFactory\OperationCommandFactory($this->commandFactory));
        
        $this->functionCommandFactory = new Command\CommandFactory\FunctionCommandFactory($this->commandFactory);
        $this->commandFactory->registerFactory('function', $this->functionCommandFactory);
        $this->registerDefaultFunctions();
        
    }
    
    protected function registerDefaultFunctions() {
        $functions = array('pi', 'pow', 'cos', 'sin', 'sqrt');
        foreach ($functions as $function) {
            $this->functionCommandFactory->registerFunction($function, $function);    
        }
        $this->functionCommandFactory->registerFunction('modulo', function($a, $b) {return $a % $b;});    
    }
    
    /**
     * Compile an expression and return the corresponding executable
     * 
     * @param string $expression
     * @return \FormulaInterpreter\Executable
     */
    function compile($expression) {
        $options = $this->parser->parse($expression);
        $command = $this->commandFactory->create($options);
        return new Executable($command, $this->variables);
    }
    
}

?>
