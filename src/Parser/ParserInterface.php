<?php

namespace Mormat\FormulaInterpreter\Parser;

interface ParserInterface
{
    /**
     * @param string $expression @todo set type 'string' instead
     * @return array
     */
    public function parse($expression);
}
