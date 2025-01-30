<?php

namespace Mormat\FormulaInterpreter\Parser;

class UnaryOperatorParser implements ParserInterface
{
    public function __construct(
        protected ParserInterface $childParser
    ) {
    }
    
    public function parse($expression): array
    {
        $operator = 'not';
        if (!str_starts_with($expression, $operator)) {
            throw new ParserException($expression);
        }
        
        $value = substr($expression, strlen($operator));
        if (!in_array($value[0], [' ', '(', '['])) {
            throw new ParserException($expression);
        }

        return [
            'type'     => 'unary_operator',
            'operator' => 'not',
            'value'    => $this->childParser->parse($value)
        ];
    }
}
