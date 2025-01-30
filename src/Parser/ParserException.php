<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Exception as BaseException;

/**
 * Thrown when an error occured during parsing
 */
class ParserException extends BaseException
{
    public function __construct(protected string $expression)
    {
        $message = sprintf('Failed to parse expression %s', $expression);
        parent::__construct($message);
    }

    public function getExpression(): string
    {
        return $this->expression;
    }
}
