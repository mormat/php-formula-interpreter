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
class OperationCommand implements CommandInterface
{
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
    protected $otherOperands = [];

    public function __construct(CommandInterface $firstOperand)
    {
        $this->firstOperand = $firstOperand;
    }

    public function addOperand($operator, CommandInterface $command)
    {
        $this->otherOperands[] = [
            'operator' => $operator,
            'command' => $command
        ];
    }

    public function run()
    {
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

    public function getParameters()
    {
        $parameters = $this->firstOperand->getParameters();

        foreach ($this->otherOperands as $otherOperand) {
            $command = $otherOperand['command'];
            $parameters = array_merge($parameters, $command->getParameters());
        }

        return $parameters;
    }
}
