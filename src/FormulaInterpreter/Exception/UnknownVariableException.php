<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class UnknownVariableException extends BaseException {
    
    protected $name;
    
    function __construct($name) {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown variable "%s"', $name));
    }
    
    public function getName() {
        return $this->name;
    }

}
