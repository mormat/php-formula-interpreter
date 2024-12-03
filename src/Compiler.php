<?php

namespace Mormat\FormulaInterpreter;

use Mormat\FormulaInterpreter\Exception\CustomFunctionNotCallableException;

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
     * @var Functions\FunctionInterface[]
     */
    protected $functions = [];
    
    function __construct() {
        
        $this->parser = new Parser\CompositeParser();
        $this->parser->addParser(new Parser\LeadingWhitespaceParser($this->parser));
        $this->parser->addParser(new Parser\WrappingParenthesisParser($this->parser));
        $this->parser->addParser(new Parser\OperationParser($this->parser));
        $this->parser->addParser(new Parser\FunctionParser($this->parser));
        $this->parser->addParser(new Parser\ArrayParser($this->parser));
        $this->parser->addParser(new Parser\VariableParser());
        $this->parser->addParser(new Parser\StringParser());
        $this->parser->addParser(new Parser\NumericParser());
        
        $this->commandFactory = new Command\CommandFactory();
        $this->commandFactory->registerFactory('numeric', new Command\CommandFactory\NumericCommandFactory());
        $this->commandFactory->registerFactory('string', new Command\CommandFactory\StringCommandFactory());
        $this->commandFactory->registerFactory('variable', new Command\CommandFactory\VariableCommandFactory());
        $this->commandFactory->registerFactory('array', new Command\CommandFactory\ArrayCommandFactory($this->commandFactory));
        $this->commandFactory->registerFactory('operation', new Command\CommandFactory\OperationCommandFactory($this->commandFactory));
        
        $functionCommandFactory = new Command\CommandFactory\FunctionCommandFactory($this->commandFactory);
        $this->commandFactory->registerFactory('function', $functionCommandFactory);
        
        foreach ($this->buildDefaultFunctions() as $function) {
            $this->registerCustomFunction($function);
        }
        
    }
    
    /**
     * @return Parser\ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }
    
    protected function buildDefaultFunctions() {

        $phpFunctions = array(
            array(['pi'], []),
            array(['cos', 'sin', 'sqrt'], ['numeric']),
            array(['pow'], ['numeric', 'numeric']),
        );
        
        foreach ($phpFunctions as $phpFunction) {
            list($callables, $supportedTypes) = $phpFunction;
            foreach ($callables as $callable) {
                yield new Functions\CallableFunction($callable, $callable, $supportedTypes);
            }  
        }
        
        $aliases = array(
            'lowercase'  => ['strtolower', ['string']],
            'uppercase'  => ['strtoupper', ['string']],
            'capitalize' => ['ucfirst',    ['string']],
        );
        foreach ($aliases as $name => $alias) {
            list($callable, $supportedTypes) = $alias;
            yield new Functions\CallableFunction($name, $callable, $supportedTypes);
        }
        
        yield new Functions\CallableFunction(
            'modulo', 
            function($a, $b) {
                return $a % $b;
            }, 
            ['numeric', 'numeric']
        );
        yield new Functions\CallableFunction(
            'count', 
            function($a) {
                return is_array($a) ? sizeof($a) : strlen($a);
            }, 
            ['string|array']
        );
    }
    
    /**
     * @param Functions\FunctionInterface $function
     */
    public function registerCustomFunction(Functions\FunctionInterface $function) {
        $this->functions[$function->getName()] = $function;
    }
    
    /**
     * Get a list of available operators
     * 
     * @return array
     */
    public function getAvailableOperators()
    {
        $operators = [
            '+' => 'add',
            '-'  => 'subtract',
            '*'  => 'multiply',
            '/'  => 'divide',
            '<'  => 'lower',
            '>'  => 'greater',
            '='  => 'equal',
            "<=" => 'lower_or_equal',
            ">=" => 'greater_or_equal',
            "in" => 'in'
        ];
       
        $results = [];
        $supportedTypes = Command\OperationCommand::getSupportedTypes();
        foreach ($operators as $operator => $name) {
            $results[$operator] = [
                'name' => $name,
                'supportedTypes' => $supportedTypes[$operator]
            ];
        };
        return $results;
        
    }
    
    /**
     * Get a list of registered functions
     */
    public function getRegisteredFunctions()
    {
        $functions = [];
        
        foreach ($this->functions as $function) {
            
            $supportedTypes = null;
            if ($function instanceof Functions\CallableFunction) {
                $supportedTypes = $function->getSupportedTypes();
            }
            
            
            $functions[$function->getName()] = array(
                'name'           => $function->getName(),
                'supportedTypes' => $supportedTypes
            );
        }
        
        return $functions;
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
        return new Executable($command, $this->functions);
    }
    
}
