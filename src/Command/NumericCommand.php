<?php

namespace Mormat\FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class NumericCommand implements CommandInterface {
    
    /**
     * @var integer
     */
    protected $value;
    
    function __construct($value) {
        if (!is_numeric($value)) {
            $message = sprintf(
                'Parameter $value of method __construct() of class %s must be an integer. Got %s type instead.', 
                get_class($this), 
                gettype($value)
            );
            throw new \InvalidArgumentException($message);
        }
        
        $this->value = $value;
    }
    
    public function run(CommandContext $context) {
        return $this->value;
    }
    
    static function create($options) {
        
    }
    
}
