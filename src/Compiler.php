<?php

namespace Mormat\FormulaInterpreter;

use Exception\CustomFunctionNotCallableException;

class Compiler
{
    protected Parser\CompositeParser $parser;
    
    protected Command\CommandFactory $commandFactory;
    
    /**
     * @var Functions\FunctionInterface[]
     */
    protected array $functions = [];
    
    public function __construct()
    {
        $this->parser = new Parser\CompositeParser();
        $this->parser->addParser(new Parser\LeadingWhitespaceParser($this->parser));
        $this->parser->addParser(new Parser\WrappingParenthesisParser($this->parser));
        $this->parser->addParser(new Parser\UnaryOperatorParser($this->parser));
        $this->parser->addParser(new Parser\OperationParser($this->parser));
        $this->parser->addParser(new Parser\FunctionParser($this->parser));
        $this->parser->addParser(new Parser\ArrayParser($this->parser));
        $this->parser->addParser(new Parser\BooleanParser());
        $this->parser->addParser(new Parser\VariableParser());
        $this->parser->addParser(new Parser\StringParser());
        $this->parser->addParser(new Parser\NumericParser());
        
        $this->commandFactory = new Command\CommandFactory();
        $commandFactories = [
            'numeric' => new Command\CommandFactory\NumericCommandFactory(),
            'string'  => new Command\CommandFactory\StringCommandFactory(),
            'boolean' => new Command\CommandFactory\BooleanCommandFactory(),
            'variable'=> new Command\CommandFactory\VariableCommandFactory(),
            'array'   => new Command\CommandFactory\ArrayCommandFactory($this->commandFactory),
            'unary_operator' => new Command\CommandFactory\UnaryOperatorCommandFactory($this->commandFactory),
            'operation' => new Command\CommandFactory\OperationCommandFactory($this->commandFactory),
            'function'  => new Command\CommandFactory\FunctionCommandFactory($this->commandFactory)
        ];
        foreach ($commandFactories as $type => $factory) {
            $this->commandFactory->registerFactory($type, $factory);
        }
        
        foreach ($this->buildDefaultFunctions() as $function) {
            $this->registerCustomFunction($function);
        }
    }
    
    public function getParser(): Parser\ParserInterface
    {
        return $this->parser;
    }
    
    protected function buildDefaultFunctions()
    {
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
            fn($a, $b) => $a % $b,
            ['numeric', 'numeric']
        );
        yield new Functions\CallableFunction(
            'count',
            fn($a) => is_array($a) ? sizeof($a) : strlen($a),
            ['string|array']
        );
    }
    
    public function registerCustomFunction(
        Functions\FunctionInterface $function
    ) {
        $this->functions[$function->getName()] = $function;
    }
    
    /**
     * Get a list of available operators
     */
    public function getAvailableOperators(): array
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
    public function getRegisteredFunctions(): array
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
     * @param string $expression
     * @return \FormulaInterpreter\Executable
     */
    public function compile($expression)
    {
        $options = $this->parser->parse($expression);
        $command = $this->commandFactory->create($options);
        return new Executable($command, $this->functions);
    }
}
