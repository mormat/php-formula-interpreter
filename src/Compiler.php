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
        
        /**
         * The most complex parsers should be on top
         */
        $this->parser = new Parser\CompositeParser();
        $this->parser->addParser(new Parser\OperatorParser($this->parser));
        $this->parser->addParser(new Parser\FunctionParser($this->parser));
        $this->parser->addParser(new Parser\ArrayParser($this->parser));
        $this->parser->addParser(new Parser\VariableParser());
        $this->parser->addParser(new Parser\StringParser());
        $this->parser->addParser(new Parser\NumericParser());
        
        
        $this->variables = new \ArrayObject();
        
        $this->commandFactory = new Command\CommandFactory();
        $this->commandFactory->registerFactory('numeric', new Command\CommandFactory\NumericCommandFactory());
        $this->commandFactory->registerFactory('string', new Command\CommandFactory\StringCommandFactory());
        $this->commandFactory->registerFactory('variable', new Command\CommandFactory\VariableCommandFactory($this->variables));
        $this->commandFactory->registerFactory('array', new Command\CommandFactory\ArrayCommandFactory($this->commandFactory));
        $this->commandFactory->registerFactory('operation', new Command\CommandFactory\OperationCommandFactory($this->commandFactory));
        // $this->commandFactory->registerFactory('array', new Command\CommandFactory\C)
        
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

        $phpFunctions = array(
            array(['pi'], []),
            array(['cos', 'sin', 'sqrt'], ['numeric']),
            array(['pow'], ['numeric', 'numeric']),
        );
        
        foreach ($phpFunctions as $phpFunction) {
            list($callables, $supportedTypes) = $phpFunction;
            foreach ($callables as $callable) {
                $function = new Functions\CallableFunction($callable, $callable, $supportedTypes);
                $this->functionCommandFactory->registerFunction($function);
            }  
        }
        
        $aliases = array(
            'lowercase'  => ['strtolower', ['string']],
            'uppercase'  => ['strtoupper', ['string']],
            'capitalize' => ['ucfirst',    ['string']],
        );
        foreach ($aliases as $name => $alias) {
            list($callable, $supportedTypes) = $alias;
            $function = new Functions\CallableFunction($name, $callable, $supportedTypes);
            $this->functionCommandFactory->registerFunction($function);
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
                'count', 
                function($a) {
                    return is_array($a) ? sizeof($a) : strlen($a);
                }, 
                ['string|array']
            )           
        );
    }
    
    /**
     * @param Functions\FunctionInterface $function
     */
    public function registerCustomFunction(Functions\FunctionInterface $function) {
        $this->functionCommandFactory->registerFunction($function);
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
