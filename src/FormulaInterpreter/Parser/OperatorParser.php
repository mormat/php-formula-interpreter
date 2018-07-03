<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Parser;
use FormulaInterpreter\Command\OperationCommand;

/**
 * Description of FunctionParser
 *
 * @author mathieu
 */
class OperatorParser implements ParserInterface
{
    
    /**
     * @var ParserInterface
     */
    protected $operandParser;
    
    public function __construct(ParserInterface $operandParser)
    {
        $this->operandParser = $operandParser;
    }
    
    public function parse($expression)
    {
        $expression = trim($expression);
        $operatorsLevel = [
            ['+', '-'],
            ['*', '/'],
            ['=', '!=', '<>', '>=', '>', '<=', '<'], //comparison
            ['AND', 'OR'], //boolean
        ];

        foreach ($operatorsLevel as $operators) {
            if ($this->hasOperators($expression, $operators)) {
                return $this->searchOperands($expression, $operators);
            }
        }
        throw new ParserException($expression);
    }

    public function hasOperators($expression, $operators)
    {
        foreach ($operators as $operator) {
            if ($this->hasOperator($expression, $operator)) {
                return true;
            }
        }
        return false;
    }
    
    public function hasOperator($expression, $operator)
    {
        $parenthesis = 0;
        $quotes = 0;
        foreach (range(0, strlen($expression) - 1) as $i) {
            $substring = substr($expression, $i, strlen($operator));
            if ($substring == $operator && $parenthesis == 0 && $quotes == 0) {
                return true;
            }
            switch ($expression[$i]) {
                case '(':
                    $parenthesis ++;
                    break;
                case ')':
                    $parenthesis --;
                    break;
                case '"':
                    $quotes = $quotes > 0 ? 0 : 1;
                    break;
            }
        }
        return false;
    }
    
    public function searchOperands($expression, $operators)
    {
        $operands = [];
        $exprLen = strlen($expression);

        $parenthesis = 0;
        
        $previous = 0;
        $quotes = 0;
        $lastOperator = null;
        for ($i = 0; $i < $exprLen; $i++) {
            switch ($expression[$i]) {
                case '(':
                    $parenthesis++;
                    break;
                case ')':
                    $parenthesis--;
                    break;
                case '"':
                    $quotes = $quotes > 0 ? 0 : 1;
                    break;
                default:
                    $operator = self::catchOperatorFromPosition($expression, $i, $operators);
                    if ($operator === null || $parenthesis !== 0 || $quotes !== 0) {
                        break;
                    }
                    $opLen = strlen($operator);
                    $subExpression = substr($expression, $previous, $i - $previous);
                    $operands[] = $this->createOperand($subExpression, $lastOperator);
                    $lastOperator = $operator;

                    if ($i + $opLen < $exprLen && $expression[$i + $opLen] != '(') {
                        $i += $opLen;
                        $previous = $i;
                    } else {
                        $previous = $i + $opLen;
                    }
            }
        }

        $subExpression = substr($expression, $previous, strlen($expression) - $previous);
        $operands[] = $this->createOperand($subExpression, $lastOperator);
        
        $firstOperand = array_shift($operands);
        
        return [
            'type' => 'operation',
            'firstOperand' => $firstOperand['value'],
            'otherOperands' => $operands
        ];
    }

    public static function catchOperatorFromPosition($expression, $position, $operators){
        usort($operators, function ($a, $b) {
            return strlen($a) < strlen($b);
        });
        foreach ($operators as $operator) {
            $substr = substr($expression, $position, strlen($operator));
            if ($substr === $operator) {
                return $operator;
            }
        }
        return null;
    }

    public static function getOperatorConstant($operator)
    {
        $map = [
            '+' => OperationCommand::ADD_OPERATOR,
            '-' => OperationCommand::SUBTRACT_OPERATOR,
            '*' => OperationCommand::MULTIPLY_OPERATOR,
            '/' => OperationCommand::DIVIDE_OPERATOR,
            '=' => OperationCommand::EQUAL_OPERATOR,
            '!=' => OperationCommand::NOT_EQUAL_OPERATOR,
            '<>' => OperationCommand::NOT_EQUAL_OPERATOR,
            '>' => OperationCommand::GREATER_THAN_OPERATOR,
            '>=' => OperationCommand::GREATER_THAN_OR_EQUAL_OPERATOR,
            '<' => OperationCommand::LESS_THAN_OPERATOR,
            '<=' => OperationCommand::LESS_THAN_OR_EQUAL_OPERATOR,
            'AND' => OperationCommand::AND_OPERATOR,
            'OR' => OperationCommand::OR_OPERATOR,
        ];
        return $map[$operator] ?? null;
    }
    
    public function createOperand($value, $operator = null)
    {
        $operand = [];
        $operand['operator'] = self::getOperatorConstant($operator);
        
        $value = trim($value);
        
        if ($value != '') {
            $value = self::cleanEnclosingParentheses($value);
        }

        if ($value == '') {
            throw new ParserException($value);
        }

        
        $operand['value'] = $this->operandParser->parse($value);
        return $operand;
    }

    private static function cleanEnclosingParentheses($expression) {
        $lastChar = substr($expression, -1, 1);
        if ($expression[0] == '(' && $lastChar == ')') {
            $expression = substr($expression, 1, -1);
            $expression = trim($expression);
        }
        return $expression;
    }
}
