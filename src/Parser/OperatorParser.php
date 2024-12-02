<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse operators in formulas
 *
 * @author mormat
 */
class OperatorParser implements ParserInterface {
    
    use ExpressionExploderTrait;
    
    /**
     * @var ParserInterface
     */
    protected $operandParser;
    
    /**
     * @var array
     */
    protected $operators;
    
    function __construct(ParserInterface $operandParser) {
        
        $this->operandParser = $operandParser;
        
        $this->operators = array(
            '+'  => 'add',
            '-'  => 'subtract',
            '*'  => 'multiply',
            '/'  => 'divide',
            '<'  => 'lower',
            '>'  => 'greater',
            '='  => 'equal',
            "<=" => 'lower_or_equal',
            ">=" => 'greater_or_equal',
            "in" => 'in'
        );
    }
    
    public function getOperators() {
        return $this->operators;
    }
        
    function parse($rawExpression) {

        $expression = trim($rawExpression);
        
        $priorities = array(
            ['<=', '>=', '<', '>', '='],
            ['in'],
            ['+', '-'],
            ['*', '/'],            
        );
        
        foreach ($priorities as $operators) {
            $parts = array_reverse(
                $this->explodeExpression($expression, $operators)
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
        
        if (isset($this->operators[$operator])) {
            $operand['operator'] = $this->operators[$operator];
        }
        
        $value = trim($value);
        
        if ($value != '') {
            if ($value[0] == '(' && substr($value, -1, 1) == ')') {
                // $value = substr($value, 1, -1);
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
    
}

