<?php

namespace Mormat\FormulaInterpreter;

use Mormat\FormulaInterpreter\Exception\CustomFunctionNotCallableException;

/**
 * Description of Compiler
 *
 * @author mormat
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
        $this->parser->addParser(new Parser\StringParser());
        $this->parser->addParser(new Parser\VariableParser());
        $this->parser->addParser(new Parser\FunctionParser($this->parser));
        $this->parser->addParser(new Parser\OperatorParser($this->parser));
        
        $this->variables = new \ArrayObject();
        
        $this->commandFactory = new Command\CommandFactory();
        $this->commandFactory->registerFactory('numeric', new Command\CommandFactory\NumericCommandFactory());
        $this->commandFactory->registerFactory('string', new Command\CommandFactory\StringCommandFactory());
        $this->commandFactory->registerFactory('variable', new Command\CommandFactory\VariableCommandFactory($this->variables));
        $this->commandFactory->registerFactory('operation', new Command\CommandFactory\OperationCommandFactory($this->commandFactory));
        
        $this->functionCommandFactory = new Command\CommandFactory\FunctionCommandFactory($this->commandFactory);
        $this->commandFactory->registerFactory('function', $this->functionCommandFactory);
        $this->registerDefaultFunctions();
        
    }
    
    /**
     * @return Parser\ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }
    
    protected function registerDefaultFunctions() {

        $trucs = array(
            array(['pi'], []),
            array(['cos', 'sin', 'sqrt'], [['numeric']]),
            array(['pow'], ['numeric', 'numeric']),
            
            array(['strtolower', 'strtoupper', 'ucfirst', 'strlen'], ['string']),
        );
        
        foreach ($trucs as $truc) {
            foreach ($truc[0] as $callable) {
                $function = new Functions\CallableFunction($callable, $callable, $truc[1]);
                $this->functionCommandFactory->registerFunction($function);
            }  
        }
          
        $this->functionCommandFactory->registerFunction(
            new Functions\CallableFunction(
                'modulo', 
                function($a, $b) {
                    return $a % $b;
                }, 
                ['numeric', 'numeric']
            )           
        );
        $this->functionCommandFactory->registerFunction(
            new Functions\CallableFunction(
                'concat', 
                function($a, $b) {
                    return $a . $b;
                }, 
                ['string', 'string']
            )           
        );
    }
    
    /**
     * @param Functions\FunctionInterface[] $functions
     */
    public function registerCustomFunctions(array $functions) {
        foreach ($functions as $function) {
            $this->functionCommandFactory->registerFunction($function);
        }
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
