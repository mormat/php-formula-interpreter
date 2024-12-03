<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

class OperationCommand implements CommandInterface {

    protected static $supportedTypes = array(
        '+' => ['bool|numeric', 'bool|numeric'],
        '-' => ['bool|numeric', 'bool|numeric'],
        '*' => ['bool|numeric', 'bool|numeric'],
        '/' => ['bool|numeric', 'bool|numeric'],
        'in' => ['numeric|string', 'array|string'],
        '<'  => ['numeric|string', 'numeric|string'],
        '>'  => ['numeric|string', 'numeric|string'],
        '='  => ['numeric|string', 'numeric|string'],
        '<='  => ['numeric|string', 'numeric|string'],
        '>=' => ['numeric|string', 'numeric|string'],    
    );
    
    protected static $validatorTypes = array(
        'bool'    => 'is_bool',
        'numeric' => 'is_numeric',
        'array'   => 'is_array',
        'string'  => 'is_string'
    );
    
    public function __construct(
            protected CommandInterface $left,
            protected string $operator,
            protected CommandInterface $right
    ) {
        
    }

    public function run(CommandContext $context) {

        $left = $this->left->run($context);
        $right = $this->right->run($context);

        $this->checkOperands($this->operator, $left, $right);

        switch ($this->operator) {
            case '+':
                return $left + $right;
            case '-':
                return $left - $right;
            case '*':
                return $left * $right;
            case '/':
                return $left / $right;
            case '=':
                return $left == $right;
            case '<':
                return $left < $right;
            case '<=':
                return $left <= $right;
            case '>':
                return $left > $right;
            case '>=':
                return $left >= $right;
            case 'in':
                if (is_string($right)) {
                    return str_contains($right, $left);
                }
                return in_array($left, $right);
        }
    }

    protected function checkOperands($operator, $left, $right) {
        
        $values = [$left, $right];
        foreach ($values as $n => $value) {
            $types = explode('|', self::$supportedTypes[$operator][$n]);
            
            $isValid = false;
            foreach ($types as $type) {
                $validator = self::$validatorTypes[$type];
                if (call_user_func($validator, $value)) {
                    $isValid = true;
                    break;
                }
            }
            
            if (!$isValid) {
                throw new UnsupportedOperandTypeException(sprintf(
                    'Unsupported operand types in %s operation',
                    $operator
                ));
            }
            
        }
                
    }
    
    public static function getSupportedTypes() {
        return self::$supportedTypes;
    }

}
