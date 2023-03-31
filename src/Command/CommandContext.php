<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Execution context of a command
 *
 * @author mormat
 */
class CommandContext {
     
    protected $variables = [];
    
    protected $functions = [];
    
    public function __construct($variables = [], $functions = []) {
        
        $this->variables = $variables;
        
        $this->functions = $functions;
        
    }
    
    public function hasVariable($name)
    {
        if ($this->variables instanceof \ArrayAccess) {
            return $this->variables->offsetExists($name);
        }
        if (is_array($this->variables)) {
            return array_key_exists($name, $this->variables);
        }
        return false;
    }
    
    public function getVariable($name)
    {
        if ($this->hasVariable($name)) {
            return $this->variables[$name];    
        }
    }
    
    public function hasFunction($name)
    {
        return array_key_exists($name, $this->functions);
    }
    
    public function getFunction($name)
    {
        return $this->functions[$name];
    }
    
}
