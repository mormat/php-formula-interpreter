<?php

namespace Mormat\FormulaInterpreter\Exception;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Thrown if formula contains unknow function
 *
 * @author mormat
 */
class UnknownFunctionException extends BaseException {
    
    protected $name;
    
    function __construct($name) {
        $this->name = $name;
        
        parent::__construct(sprintf('Unknown function "%s"', $name));
    }
    
    public function getName() {
        return $this->name;
    }

}
