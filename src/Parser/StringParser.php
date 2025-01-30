<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse strings in formulas
 */
class StringParser implements ParserInterface
{
    public function parse($rawExpression)
    {
        $expression = trim($rawExpression);
        
        if ($expression[0] !== "'" || $expression[-1] !== "'") {
            throw new ParserException($rawExpression);
        }
        
        $value = substr($expression, 1, -1);
        
        if (strpos($value, "'") !== false) {
            throw new ParserException($rawExpression);
        }
        
        return array(
            'type'  => 'string',
            'value' => $value,
        );
    }
}
