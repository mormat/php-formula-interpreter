<?php

namespace Mormat\FormulaInterpreter\Functions;

class CallableFunction implements FunctionInterface
{
    protected $validatorTypes = array(
        'numeric' => 'is_numeric',
        'array'   => 'is_array',
        'string'  => 'is_string'
    );
    
    public function __construct(
        protected string $name,
        protected $callable,
        protected array $supportedTypes = []
    ) {
    }
    
    public function getSupportedTypes(): array
    {
        return $this->supportedTypes;
    }
    
    public function supports(array $values): bool
    {
        
        foreach ($this->supportedTypes as $i => $rawSupportedType) {
            $supportedTypefilter = function ($supportedType) use ($i, $values) {
                if (!isset($values[$i])) {
                    return false;
                }

                $validator = $this->validatorTypes[$supportedType];
                if (!$validator($values[$i])) {
                    return false;
                }

                return true;
            };
            
            $results = array_map(
                $supportedTypefilter,
                explode('|', $rawSupportedType)
            );
            
            if (array_unique($results) == [false]) {
                return false;
            }
        }
        
        return true;
    }
    
    public function execute(array $params): mixed
    {
        return call_user_func_array($this->callable, $params);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
        
    /**
     * Checks if $type matches the provided $value
     * @param mixed  $value
     * @param string $type
     */
    protected function valueIsType($value, string $type = 'mixed'): bool
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
}
