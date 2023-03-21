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
    
    protected $supportedTypes = array(
        self::ADD_OPERATOR      => ['numeric', 'numeric'],
        self::SUBTRACT_OPERATOR => ['numeric', 'numeric'],
        self::MULTIPLY_OPERATOR => ['numeric', 'numeric'],
        self::DIVIDE_OPERATOR   => ['numeric', 'numeric'],
        self::IN_OPERATOR       => ['numeric|string', 'array|string']
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
    
    function addOperand($operator, CommandInterface $command) {
        $this->otherOperands[] = array(
            'operator' => $operator,
            'command' => $command
        );
    }
   
    public function run() {
        $result = $this->firstOperand->run();
        foreach ($this->otherOperands as $otherOperand) {
            
            $operator = $otherOperand['operator'];
            $command = $otherOperand['command'];
            
            $values  = [$result, $command->run()];
            if (!$this->operatorSupportsValues($operator, $values)) {
                throw new UnsupportedOperandTypeException(sprintf(
                    'Unsupported operand types in %s operation',
                    $operator
                ));
            }
            
            switch ($operator) {
                case self::ADD_OPERATOR:
                    $result = $result + $command->run();
                    break;
                case self::MULTIPLY_OPERATOR:
                    $result = $result * $command->run();
                    break;
                case self::SUBTRACT_OPERATOR:
                    $result = $result - $command->run();
                    break;
                case self::DIVIDE_OPERATOR:
                    $result = $result / $command->run();
                    break;
                case self::IN_OPERATOR:
                    $otherResult = $command->run();
                    if (is_array($otherResult)) {
                        return in_array($result, $otherResult);
                    } else {
                        // $otherResult contains $result
                        return (strpos($otherResult, $result) !== false);
                    }
                    
                    break;
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
        foreach ($this->supportedTypes[$operator] as $i => $rawSupportedType) {    
            
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
