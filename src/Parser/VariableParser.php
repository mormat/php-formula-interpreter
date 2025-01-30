<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse variables in formulas
 */
class VariableParser implements ParserInterface
{
    public function parse($rawExpression)
    {
        $expression = trim($rawExpression);
        
        if (!preg_match('/^([a-zA-Z_]+[0-9]*)+$/', $expression)) {
            throw new ParserException($rawExpression);
        }
        
        return array(
            'type' => 'variable',
            'name' => $expression,
        );
    }
}
