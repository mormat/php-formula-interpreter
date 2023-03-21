<?php

namespace Mormat\FormulaInterpreter\Parser;

trait ExpressionExploderTrait {

    /**
     * Explodes an expression into multiple fragments using specific separators
     * 
     * useful in complex parsers such as operators, functions and arrays
     * 
     * @param  string $expression
     * @param  array  $separators List of separators
     * @param  array  $options    
     * 
     * @return array  List of fragments
     */
    function explodeExpression($expression, array $separators, array $options = []) {

        $fragments = [];
        
        // setting default options
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
            'matchingSeparator' => null,
            'previousFragment'  => '',
        ];
        
        // Check if separators contains words (examples: 'or', 'and', 'in')
        $isWord  = function($str) {
            $nbrChars = strlen($str);
            for ($i = 0; $i < $nbrChars; $i++) {
                $dec = ord($str[$i]);
                if ($dec === 95) {
                    continue;
                }
                if (97 <= $dec && $dec < 122) {
                    continue;
                }
                if (65 <= $dec && $dec < 90) {
                    continue;
                }
                if (48 <= $dec && $dec < 57) {
                    continue;
                }
                return false;
            }
            return ($nbrChars > 0);
        };
        $wordSeparators = array_filter($separators, $isWord);
        
        list($offset, $limit) = [-1, strlen($expression)];
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

            // find if any separator matches the current fragment
            $infos->matchingSeparator = null;
            foreach ($separators as $s) {
                if (substr($expression, $offset, strlen($s)) === $s) {
                    $infos->matchingSeparator = $s;
                    break;
                }
            }
            
            // A list a requirements that the matching separator must meets before being handled
            $matchingSeparatorRequirements = [];
            if ($infos->matchingSeparator) {
                $matchingSeparatorRequirements[] = $options['canUseOperatorCallback']($infos);
            }
            
            // If matching separator is word, check that previous and next character are not also words
            if (in_array($infos->matchingSeparator, $wordSeparators)) {
                $previousChar = $offset > 0 ? $expression[$offset-1] : '';
                $nextChar     = substr($expression, $offset + strlen($infos->matchingSeparator), 1);

                $matchingSeparatorRequirements[] = !$isWord($previousChar);
                $matchingSeparatorRequirements[] = !$isWord($nextChar);
            }
            
            // finally handle the matching separator if all requirements are OK
            if (array_unique($matchingSeparatorRequirements) == [true]) {
                $fragments[] = $infos->previousFragment;
                $infos->previousFragment = '';

                $fragments[] = $infos->matchingSeparator;

                $offset += strlen($infos->matchingSeparator) - 1;
                continue;
            }
            
            $infos->previousFragment .= $expression[$offset];
        }

        $fragments[] = $infos->previousFragment;
        $fragments   = array_filter($fragments, function($str) {
            return $str !== '';
        });
        return array_values($fragments);
    }

}
