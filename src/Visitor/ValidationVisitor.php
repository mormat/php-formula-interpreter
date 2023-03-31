<?php

namespace Mormat\FormulaInterpreter\Visitor;

use \Mormat\FormulaInterpreter\Command\FunctionCommand;
use \Mormat\FormulaInterpreter\Command\VariableCommand;

class ValidationVisitor implements VisitorInterface
{
    
    protected $variables = [];
    protected $functions = [];
    
    protected $errors = [];
    
    const UNKNOWN_VARIABLE_ERROR = 'unknown_variable';
    const UNKNOWN_FUNCTION_ERROR = 'unknown_function';
    
    public function __construct($variables = array(), $functions = array()) {
        $this->variables = $variables;
        $this->functions = $functions;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function accept($subject) {
        if ($subject instanceof VariableCommand)
        {
            $this->acceptVariableCommand($subject);
        } else if ($subject instanceof FunctionCommand)
        {
            $this->acceptFunctionCommand($subject);
        }
        
    }
    
    protected function acceptVariableCommand(VariableCommand $command)
    {
        $name = $command->getVariableName();
        if (!isset($this->variables[$name])) {
            $this->errors[] = [
                'type'  => self::UNKNOWN_VARIABLE_ERROR,
                'value' => $name
            ];
        }
    }

    protected function acceptFunctionCommand(FunctionCommand $command)
    {
        $functionName = $command->getFunctionName();
        if (!isset($this->functions[$functionName])) {
            $this->errors[] = [
                'type'  => self::UNKNOWN_FUNCTION_ERROR,
                'value' => $functionName
            ];
        }
    }
    
}
