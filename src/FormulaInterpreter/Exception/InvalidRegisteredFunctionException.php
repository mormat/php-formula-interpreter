<?php

namespace Mormat\FormulaInterpreter\Exception;

/**
 * Thrown if a registered function don't implements Mormat\FormulaInterpreter\Functions
 *
 * @author mormat
 */
class InvalidRegisteredFunctionException extends \Exception {
    
    protected $name;
    
    function __construct($name) {
        $this->name = $name;
        
        parent::__construct(sprintf("Custom function '%s' must be callable", $name));
    }
    
    public function getName() {
        return $this->name;
    }

}
