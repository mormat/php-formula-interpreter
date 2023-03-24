<?php

namespace Mormat\FormulaInterpreter\Command;

use \Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

/**
 * Executes an operation command
 *
 * @author mormat
 */
class OperationCommand implements CommandInterface {
    
    const ADD_OPERATOR = 'add';
    const SUBTRACT_OPERATOR = 'subtract';
    const MULTIPLY_OPERATOR = 'multiply';
    const DIVIDE_OPERATOR = 'divide';
    const IN_OPERATOR = 'in';
    const LOWER_OPERATOR = 'lower';
    const GREATER_OPERATOR = 'greater';
    const EQUAL_OPERATOR = 'equal';
    const LOWER_OR_EQUAL_OPERATOR   = 'lower_or_equal';
    const GREATER_OR_EQUAL_OPERATOR = 'greater_or_equal';
    
    protected static $supportedTypes = array(
        self::ADD_OPERATOR      => ['numeric', 'numeric'],
        self::SUBTRACT_OPERATOR => ['numeric', 'numeric'],
        self::MULTIPLY_OPERATOR => ['numeric', 'numeric'],
        self::DIVIDE_OPERATOR   => ['numeric', 'numeric'],
        self::IN_OPERATOR       => ['numeric|string', 'array|string'],
        self::LOWER_OPERATOR    => ['numeric|string', 'numeric|string'],
        self::GREATER_OPERATOR  => ['numeric|string', 'numeric|string'],
        self::EQUAL_OPERATOR    => ['numeric|string', 'numeric|string'],
        self::LOWER_OR_EQUAL_OPERATOR   => ['numeric|string', 'numeric|string'],
        self::GREATER_OR_EQUAL_OPERATOR => ['numeric|string', 'numeric|string'],
        
    );
    
    protected $validatorTypes = array(
        'numeric' => 'is_numeric',
        'array'   => 'is_array',
        'string'  => 'is_string'
    );
    
    /**
     * @var CommandInterface
     */
    protected $firstOperand;
    
    /**
     * @var array
     */
    protected $otherOperands = array();
    
    function __construct(CommandInterface $firstOperand) {
        $this->firstOperand = $firstOperand;
    }
    
    public static function getSupportedTypes() {
        return self::$supportedTypes;
    }
    
    function addOperand($operator, CommandInterface $command) {
        $this->otherOperands[] = array(
            'operator' => $operator,
            'command' => $command
        );
    }
   
    public function run(CommandContext $context) {
        $result = $this->firstOperand->run($context);
        foreach ($this->otherOperands as $otherOperand) {
            
            $operator = $otherOperand['operator'];
            $command = $otherOperand['command'];
            
            $values  = [$result, $command->run($context)];
            if (!$this->operatorSupportsValues($operator, $values)) {
                throw new UnsupportedOperandTypeException(sprintf(
                    'Unsupported operand types in %s operation',
                    $operator
                ));
            }
           
            /**
             * @todo [refactor] each case can directly return value
             */
            switch ($operator) {
                case self::ADD_OPERATOR:
                    $result = $result + $command->run($context);
                    break;
                case self::MULTIPLY_OPERATOR:
                    $result = $result * $command->run($context);
                    break;
                case self::SUBTRACT_OPERATOR:
                    $result = $result - $command->run($context);
                    break;
                case self::DIVIDE_OPERATOR:
                    $result = $result / $command->run($context);
                    break;
                case self::IN_OPERATOR:
                    $otherResult = $command->run($context);
                    if (is_array($otherResult)) {
                        return in_array($result, $otherResult);
                    } else {
                        // $otherResult contains $result
                        return (strpos($otherResult, $result) !== false);
                    }
                    
                    break;
                case self::LOWER_OPERATOR:
                    return $result < $command->run($context);
                case self::GREATER_OPERATOR:
                    return $result > $command->run($context);
                case self::EQUAL_OPERATOR:
                    return $result == $command->run($context);
                case self::LOWER_OR_EQUAL_OPERATOR:
                    return $result <= $command->run($context);
                case self::GREATER_OR_EQUAL_OPERATOR:
                    return $result >= $command->run($context);
            }
            
        }
        return $result;
    }
    
    /**
     * Returns true if $operator supports the provided $values
     * 
     * @param string $operator
     * @param array  $values
     * @return boolean
     */
    protected function operatorSupportsValues($operator, $values)
    {
        foreach (self::$supportedTypes[$operator] as $i => $rawSupportedType) {    
            
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
}
