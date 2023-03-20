<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * @author mormat
 */
interface ParserInterface {
    
    /**
     * @param  string $expression
     * @return array
     */
    function parse($expression);
    
}
