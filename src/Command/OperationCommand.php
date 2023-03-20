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
    
    protected $supportedTypes = array(
        self::ADD_OPERATOR      => ['numeric', 'numeric'],
        self::SUBTRACT_OPERATOR => ['numeric', 'numeric'],
        self::MULTIPLY_OPERATOR => ['numeric', 'numeric'],
        self::DIVIDE_OPERATOR   => ['numeric', 'numeric'],
    );
    
    protected $validatorTypes = array(
        'numeric' => 'is_numeric',
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
            if (!$this->areValuesValid($values, $operator)) {
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
            }
            
        }
        return $result;
    }
    
    protected function areValuesValid($values, $operator)
    {
        foreach ($this->supportedTypes[$operator] as $i => $supportedType) {            
            if (!isset($values[$i])) {
                return false;
            }
            
            $validator = $this->validatorTypes[$supportedType];
            if (!$validator($values[$i])) {
                return false;
            }
        }
        
        return true;
    }
}
