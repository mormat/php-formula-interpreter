<?php

namespace Mormat\FormulaInterpreter\Parser;

class BooleanParser implements ParserInterface
{
    public function parse($expression)
    {
        if (in_array($expression, ['true', 'false'])) {
            return [
                'type' => 'boolean',
                'value' => $expression === 'true'
            ];
        }
        
        throw new ParserException($expression);
    }
}
