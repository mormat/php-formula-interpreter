<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class VariableCommand implements CommandInterface {
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var variables
     */
    protected $variables;
    
    function __construct($name, $variables) {
        if (!is_string($name)) {
            $message = sprintf(
                'Parameter $name of method __construct() of class %s must be a string. Got %s type instead.', 
                get_class($this), 
                gettype($name)
            );
            throw new \InvalidArgumentException($message);
        }
        $this->name = $name;
        
        if (!(is_array($variables) || $variables instanceof \ArrayAccess)) {
            $message = sprintf(
                'Parameter $variables of method __construct() of class %s must be an array or implements ArrayAccess interface. Got %s type instead.', 
                get_class($this), 
                gettype($name)
            );
            throw new \InvalidArgumentException($message);
        }
        $this->variables = $variables;
    }

    public function run() {
        if(!isset($this->variables[$this->name])) {
            throw new \FormulaInterpreter\Exception\UnknownVariableException($this->name);
        }
        
        return $this->variables[$this->name];
    }
}

?>
