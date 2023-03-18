<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Execute of a string expression
 *
 * @author mormat
 */
class StringCommand implements CommandInterface {
    
    /**
     * @var integer
     */
    protected $value;
    
    function __construct($value) {
        if (!is_string($value)) {
            $message = sprintf(
                'Parameter $value of method __construct() of class %s must be a string. Got %s type instead.', 
                get_class($this), 
                gettype($value)
            );
            throw new \InvalidArgumentException($message);
        }
        
        $this->value = $value;
    }
    
    public function run() {
        return $this->value;
    }
    
    
}
