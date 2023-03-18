<?php

namespace Mormat\FormulaInterpreter\Functions;

class CallableNumericFunction implements FunctionInterface {
    
    /**
     * @var callable
     */
    protected $callable;
    
    /**
     * min number of parameters
     * 
     * @var int
     */
    protected $minParams;
    
    /**
     * max number of parameters
     * 
     * @var int
     */
    protected $maxParams;
    
    public function __construct($callable, $minParams = 1, $maxParams = 2) {
        $this->callable  = $callable;
        $this->minParams = $minParams;
        $this->maxParams = $maxParams;
    }
    
    public function supports($params) {
        if (count($params) < $this->minParams) {
            return false;
        }
        
        if (count($params) > $this->maxParams) {
            return false;
        }
        
        if (is_int($params[0]) || is_float($params[0]) || is_numeric($params[0])) {
            return true;
        }
        return false;
    }

    
    public function execute($params) {
        return call_user_func_array($this->callable, $params);
    }
    
    public function getName() {
        return '@todo put function name here';
    }

}