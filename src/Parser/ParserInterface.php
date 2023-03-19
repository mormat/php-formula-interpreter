<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
interface ParserInterface {
    
    /**
     * @param  string $expression
     * @return array
     */
    function parse($expression);
    
}
