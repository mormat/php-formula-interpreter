<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Exception;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class UnknownVariableException extends \Exception {
    
    protected $name;
    
    function __construct($name) {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown variable "%s"', $name));
    }
    
    public function getName() {
        return $this->name;
    }

}

?>
