<?php

namespace Mormat\FormulaInterpreter\Exception;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class UnknownFunctionException extends \Exception {
    
    protected $name;
    
    function __construct($name) {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown function "%s"', $name));
    }
    
    public function getName() {
        return $this->name;
    }

}
