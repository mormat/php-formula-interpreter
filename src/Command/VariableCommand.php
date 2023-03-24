<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\UnknownVariableException;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class VariableCommand implements CommandInterface {
    
    /**
     * @var string
     */
    protected $name;
        
    function __construct($name) {
        if (!is_string($name)) {
            $message = sprintf(
                'Parameter $name of method __construct() of class %s must be a string. Got %s type instead.', 
                get_class($this), 
                gettype($name)
            );
            throw new \InvalidArgumentException($message);
        }
        $this->name = $name;
    }

    public function run(CommandContext $context) {
        if (!$context->hasVariable($this->name)) {
            throw new UnknownVariableException($this->name);
        }
        
        return $context->getVariable($this->name);
        
        /*
        if(!isset($this->variables[$this->name])) {
            throw new UnknownVariableException($this->name);
        }
        
        return $this->variables[$this->name];*/
    }
}
