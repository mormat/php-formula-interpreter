<?php

namespace Mormat\FormulaInterpreter\Visitor;

use \Mormat\FormulaInterpreter\Command\VariableCommand;

class ValidationVisitor implements VisitorInterface
{
    
    protected $variables = [];
    
    protected $errors = [];
    
    const UNKNOWN_VARIABLE_ERROR = 'unknown_variable';
    
    public function __construct($variables = array()) {
        $this->variables = $variables;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function accept($subject) {
        if ($subject instanceof VariableCommand)
        {
            $this->acceptVariableCommand($subject);
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

}
