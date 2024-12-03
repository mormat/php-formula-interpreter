<?php

namespace Mormat\FormulaInterpreter\Parser;

class OperationParser implements ParserInterface {
    
    const ADD_OPERATOR = '+';
    const SUBSTRACT_OPERATOR = '-';
    const MULTIPLY_OPERATOR = '*';
    const DIVIDE_OPERATOR = '/';
    const IN_OPERATOR = 'in';
    const LOWER_THAN_OPERATOR = '<';
    const GREATER_THAN_OPERATOR = '>';
    const EQUAL_OPERATOR = '=';
    const LOWER_OR_EQUAL_OPERATOR = '<=';
    const GREATER_OR_EQUAL_OPERATOR = '>=';
    
    public function __construct(
        protected ParserInterface $childParser
    ) { }

    
    public function parse($expression): array {
        
        $orderedOperators = [
            self::LOWER_OR_EQUAL_OPERATOR,
            self::LOWER_THAN_OPERATOR, 
            self::GREATER_OR_EQUAL_OPERATOR,
            self::GREATER_THAN_OPERATOR,
            self::EQUAL_OPERATOR,
            self::IN_OPERATOR, 
            self::ADD_OPERATOR, 
            self::SUBSTRACT_OPERATOR, 
            self::MULTIPLY_OPERATOR, 
            self::DIVIDE_OPERATOR,
            
        ];
        
        foreach ($orderedOperators as $operator) {
            
            $operands = $this->extractOperands($expression, $operator);
            if ($operands === null) {
                continue;
            }
            
            return [
                'type'     => 'operation',
                'left'     => $this->childParser->parse($operands[0]),
                'operator' => $operator,
                'right'    => $this->childParser->parse($operands[1])
            ];

        }
        
        throw new ParserException($expression);
        
    }
    
    protected function extractOperands($expression, $operator) {
            
        $pos   = $this->findOperatorPosition($expression, $operator);
        if (!$pos) {
            return null;
        }
        
        $left  = substr($expression, 0, $pos);
        $right = substr($expression, $pos + strlen($operator));
        
        if ($operator === self::IN_OPERATOR) {
            if (!in_array($right[0], ['[', ' ', '('])) {
                return null;
            }
            if (!in_array($left[-1], [']', ' ', ')'])) {
                return null;
            }
            $left  = trim($left);
            $right = trim($right);
        }
        
        return [$left, $right];
            
    }
      
    protected function findOperatorPosition($expression, $operator) {
        
        $chars = str_split($expression);
        $openedParenthesis = 0;
        $openedString = false;
        $openedArrays = 0;
        foreach ($chars as $pos => $char) {
            if ($char === "'") $openedString = !$openedString;
            if ($openedString) continue;
            
            if ($char === '(') $openedParenthesis++;
            if ($char === ')') $openedParenthesis--;
            if ($openedParenthesis > 0) continue;
            
            if ($char === '[') $openedArrays++;
            if ($char === ']') $openedArrays--;
            if ($openedArrays > 0) continue;
                        
            $substr = substr($expression, $pos, strlen($operator));
            if ($substr === $operator) {
                return $pos;
            }
        }
        
        return null;
        
    }
    
}
