<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author Petra Barus <petra.barus@gmail.com>
 */
class StringParser implements ParserInterface
{
    const PATTERN = '/^".+"$/';

    public function parse($expression)
    {
        $expression = trim($expression);
        
        if (!preg_match(self::PATTERN, $expression)) {
            throw new ParserException($expression);
        }

        $string = substr($expression, 1, strlen($expression) - 2);
        $string = str_replace("\\\"", "\"", $string);
        return $infos = [
            'type' => 'string',
            'value' => (string) $string,
        ];
    }
}
