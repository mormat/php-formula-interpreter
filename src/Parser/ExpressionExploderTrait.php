<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * explodes an expression into multiple fragments using specific separators
 * 
 * useful in complex parsers such as operators, functions and arrays
 */
trait ExpressionExploderTrait {

    function explodeExpression($expression, array $separators, array $options = []) {

        $options += array(
            
            'canUseOperatorCallback' => function ($infos) {
                if ($infos->openedBrackets > 0) {
                    return false;
                }

                if ($infos->openedParenthesis > 0) {
                    return false;
                }

                if ($infos->betweenQuotes) {
                    return false;
                }

                return true;
            }
            
        );

        // gathering infos when iterating over expression
        $infos = (object) [
            'openedBrackets'    => 0,
            'openedParenthesis' => 0,
            'betweenQuotes'     => false,
        ];

        $results = [];
        $fragment = '';
        $offset = -1;
        $limit = strlen($expression);
        while (++$offset < $limit) {

            if ($expression[$offset] == '[') {
                $infos->openedBrackets++;
            }
            if ($expression[$offset] == ']') {
                $infos->openedBrackets--;
            }
            if ($expression[$offset] == '(') {
                $infos->openedParenthesis++;
            }
            if ($expression[$offset] == ')') {
                $infos->openedParenthesis--;
            }
            if ($expression[$offset] == "'") {
                $infos->betweenQuotes = !$infos->betweenQuotes;
            }

            $foundSeparator = null;
            foreach ($separators as $separator) {

                if (substr($expression, $offset, strlen($separator)) === $separator) {
                    $foundSeparator = $expression[$offset];
                    break;
                }
            }

            if ($foundSeparator && $options['canUseOperatorCallback']($infos)) {
                $results[] = $fragment;
                $fragment = '';

                $results[] = $separator;

                $offset += strlen($separator) - 1;
                continue;
            }

            $fragment .= $expression[$offset];
        }

        $results[] = $fragment;
        $results = array_filter($results, function($str) {
            return $str !== '';
        });
        return array_values($results);
    }

}
