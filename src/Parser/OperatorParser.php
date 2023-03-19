<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse operators in formulas
 *
 * @author mormat
 */
class OperatorParser implements ParserInterface {
    
    /**
     * @var ParserInterface
     */
    protected $operandParser;
    
    function __construct(ParserInterface $operandParser) {
        $this->operandParser = $operandParser;
    }
    
    function parse($rawExpression) {

        $expression = trim($rawExpression);
        
        $priorities = array(
            ['+', '-'],
            ['*', '/']
        );
        
        foreach ($priorities as $operators) {
            $parts = array_reverse(
                self::splitExpressionByOperators($expression, $operators)
            );
            if (count($parts) == 1) {
                continue;
            }
            
            $otherOperands  = [];
            $handledParts   = [];
            $remainingParts = $parts;

            while (sizeof($parts) > 0) {
                
                $part = array_shift($parts);
                
                if (in_array($part, $operators)) {
                    $value    = join('', $handledParts);
                    $operator = $part;
                    
                    $operand = $this->createOperand($value, $operator);
                    
                    $otherOperands[] = $operand;
                    $remainingParts  = array_splice(
                        $remainingParts, 
                        -count($handledParts)
                    );
                    $handledParts = [];
                } else {
                    $handledParts[]  = $part;  
                }
                
            }
                
            // check validity of other operands
            foreach ($otherOperands as $operand) {
                if (!$operand['value']) {
                    throw new ParserException($rawExpression);
                }
            }
            
            $firstOperand = join('', $remainingParts);
            return array(
                'type'          => 'operation',
                'firstOperand'  => $this->operandParser->parse($firstOperand),
                'otherOperands' => array_reverse($otherOperands)
            );
            
        }
        
        // If nothing was found and the expression is wrapped between parenthesis
        // then extract content between parenthesis
        // @todo test ParserException here
        if ($expression[0] == '(' && $expression[-1] == ')') {
            try {
                return $this->parse(substr($expression, 1, -1));
            } catch (ParserException $ex) {
                throw new ParserException($rawExpression);
            }
            
        }
                
        throw new ParserException($rawExpression);
        
    }
    
    function createOperand($value, $operator = null) {
        $operand = array();
        switch ($operator) {
            case '+':
                $operand['operator'] = 'add';
                break;
            case '-':
                $operand['operator'] = 'subtract';
                break;
            case '*':
                $operand['operator'] = 'multiply';
                break;
            case '/':
                $operand['operator'] = 'divide';
                break;
        }
        
        $value = trim($value);
        
        if ($value != '') {
            if ($value[0] == '(' && substr($value, -1, 1) == ')') {
                $value = substr($value, 1, -1);
                $value = trim($value);
            }
        }

        if ($value != '') {
            $operand['value'] = $this->operandParser->parse($value);
        } else {
            $operand['value'] = null;
        }
        return $operand;
    }
    
    
    public static function splitExpressionByOperators($rawExpression, $operators)
    {
        $expression = trim($rawExpression);
        if (!$expression) {
            return [];
        }
        
        $results  = [];
        $fragment = '';
        $openedParenthesis = 0;
        $betweenQuotes     = false;
        
        $offset = 0;
        $limit  = strlen($expression);
        while ($offset < $limit) {
            
            foreach ($operators as $operator) {
                
                if (substr($expression, $offset, strlen($operator)) != $operator) {
                    continue;
                }
                
                if ($openedParenthesis > 0 || $betweenQuotes) {
                    continue;
                }

                if ($fragment) {
                    $results[] = $fragment;
                    $fragment = '';      
                }

                $results[] = $operator;
                $offset += strlen($operator);

            }
            
            if (!($offset < $limit)) {
                continue;
            }
            
            if ($expression[$offset] == '(') {
                $openedParenthesis++;
            } else if ($expression[$offset] == ')') {
                $openedParenthesis--;
            }
            
            if ($expression[$offset] == "'") {
                $betweenQuotes = !$betweenQuotes;
            }
            
            $fragment .= $expression[$offset];
            $offset   += 1;
        }
        
        if ($fragment) {
            $results[] = $fragment;
        }
        

        
        return $results;
    }
    
}

