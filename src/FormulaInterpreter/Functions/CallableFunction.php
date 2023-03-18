<?php

namespace Mormat\FormulaInterpreter\Functions;

class CallableFunction implements FunctionInterface {
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var callable
     */
    protected $callable;
    
    /**
     * @var string[]
     */
    protected $supportedTypes;
    
    public function __construct($name, $callable, array $supportedTypes = []) {
        $this->name     = $name;
        $this->callable = $callable; // @todo tests if callable is really callable
        $this->supportedTypes = $supportedTypes;
    }
    
    public function supports(array $params) {
        foreach ($this->supportedTypes as $i => $supportedType) {            
            if (!isset($params[$i])) {
                return false;
            }
            
            if (!$this->valueIsType($params[$i], $supportedType)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Checks if $type matches the provided $value 
     * 
     * @param mixed  $value
     * @param string $type
     * @return boolean
     */
    protected function valueIsType($value, $type = 'mixed')
    {
        switch ($type) {
            case 'numeric':
                return $this->isNumeric($value);
            case 'string':
                return is_string($value);
        }
        return true;
    }

    /**
     * @todo put this in a helper ?
     * 
     * @param mixed $value
     * @return boolean
     */
    protected function isNumeric($value)
    {
        if (is_int($value) || is_float($value) || is_numeric($value)) {
            return true;
        }
        return false;
    }
    
    public function execute(array $params) {
        return call_user_func_array($this->callable, $params);
    }
    
    public function getName() {
        return $this->name;
    }

}