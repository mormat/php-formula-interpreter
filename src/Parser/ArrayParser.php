<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Parser\ParserInterface;

/**
 * Parse arrays in formulas
 *
 * @author mormat
 */
class ArrayParser implements ParserInterface  {
    
    /**
     * @var ParserInterface
     */
    protected $itemParser;
    
    public function __construct(ParserInterface $itemParser) {
        $this->itemParser = $itemParser;
    }

    public function parse($rawExpression) {
        
        $expression = trim($rawExpression);
        
        if (!$expression) {
            throw new ParserException($rawExpression);
        }
        
        if ($expression[0] == '[' && $expression[-1] == ']') {
            $itemsExpression = substr($expression, 1, -1);
            
            $separators = [','];
            $fragments  = $this->explode($itemsExpression, $separators);
            $items      = array_diff($fragments, $separators);
            $values     = array_map([$this->itemParser, 'parse'], $items);
            
            return array(
                'type'  => 'array',
                'value' => array_values($values),
            );
        }
        
        throw new ParserException($rawExpression);
    }
    
    public function explode($expression, array $separators)
    {
        $infos = (object) [
            'openedBrackets'    => 0,
            'openedParenthesis' => 0,
            'betweenQuotes'     => false,
        ];
        
        $canUseOperator  = function($separator) use ($infos) {
           
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
        };
        
        $results  = [];
        $fragment = '';
        $offset   = -1;
        $limit    = strlen($expression);
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
                if ($separator == $expression[$offset]) {
                    $foundSeparator = $expression[$offset];
                    break;
                }
            }
            
            if ($foundSeparator && $canUseOperator($separator)) {
                
                $results[] = $fragment;
                $fragment  = '';

                $results[] = $expression[$offset];
                continue;
            }
            
            $fragment .= $expression[$offset];
            
        }
        
        $results[] = $fragment;
        $results   = array_values(array_filter($results));
        return $results;
    }

}
