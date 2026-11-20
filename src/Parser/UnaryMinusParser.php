<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Parse a negated expression such as '-2', '-x' or '-(1 + 2)'
 *
 * Must run after the OperationParser, so that '-2 - 3' is read as '(-2) - 3'
 * and not as '-(2 - 3)'
 */
class UnaryMinusParser implements ParserInterface
{
    public function __construct(
        protected ParserInterface $childParser
    ) {
    }

    public function parse($expression): array
    {
        $operator = '-';
        if (!str_starts_with($expression, $operator)) {
            throw new ParserException($expression);
        }

        $value = substr($expression, strlen($operator));
        if (trim($value) === '') {
            throw new ParserException($expression);
        }

        return [
            'type'     => 'unary_operator',
            'operator' => $operator,
            'value'    => $this->childParser->parse($value)
        ];
    }
}
