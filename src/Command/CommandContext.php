<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Describes the execution context of a command
 *
 * @author mormat
 */
class CommandContext {
     
    protected $variables;
    
    public function __construct($variables = []) {
        
        if (!(is_array($variables) || $variables instanceof \ArrayAccess)) {
            $message = sprintf(
                'Parameter $variables of method __construct() of class %s must be an array or implements ArrayAccess interface. Got %s type instead.', 
                get_class($this), 
                gettype($variables)
            );
            throw new \InvalidArgumentException($message);
        }
        $this->variables = $variables;
        
    }
    
    public function hasVariable($name)
    {
        if ($this->variables instanceof \ArrayAccess) {
            return $this->variables->offsetExists($name);
        }
        return array_key_exists($name, $this->variables);
    }
    
    public function getVariable($name)
    {
        if ($this->hasVariable($name)) {
            return $this->variables[$name];    
        }
    }
    
}
