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
    
    protected $validatorTypes = array(
        'numeric' => 'is_numeric',
        'array'   => 'is_array',
        'string'  => 'is_string'
    );
    
    public function __construct($name, $callable, array $supportedTypes = []) {
        $this->name     = $name;
        $this->callable = $callable; // @todo tests if callable is really callable
        $this->supportedTypes = $supportedTypes;
    }
    
    public function supports(array $values) {
        
        foreach ($this->supportedTypes as $i => $rawSupportedType) {   
            
            $results = array_map(function($supportedType) use ($i, $values) {
                
                if (!isset($values[$i])) {
                    return false;
                }

                $validator = $this->validatorTypes[$supportedType];
                if (!$validator($values[$i])) {
                    return false;
                }
                
                return true;
                
            }, explode('|', $rawSupportedType));
            
            if (array_unique($results) == [false]) {
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
                return is_numeric($value);
            case 'string':
                return is_string($value);
            case 'array':
                return is_array($value);
        }
        return true;
    }
    
    public function execute(array $params) {
        return call_user_func_array($this->callable, $params);
    }
    
    public function getName() {
        return $this->name;
    }

}