<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Command;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class OperationCommand implements CommandInterface {
    
    const ADD_OPERATOR = 'add';
    const SUBTRACT_OPERATOR = 'subtract';
    const MULTIPLY_OPERATOR = 'multiply';
    const DIVIDE_OPERATOR = 'divide';
    
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
}

?>
