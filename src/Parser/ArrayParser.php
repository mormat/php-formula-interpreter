<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Parser\ParserInterface;

/**
 * Parse arrays in formulas
 */
class ArrayParser implements ParserInterface
{
    use ExpressionExploderTrait;
    
    public function __construct(
        protected ParserInterface $itemParser
    ) {
    }

    public function parse($rawExpression)
    {
        $expression = trim($rawExpression);
        
        if (!$expression) {
            throw new ParserException($rawExpression);
        }
        
        if ($expression[0] == '[' && $expression[-1] == ']') {
            $itemsExpression = substr($expression, 1, -1);
            
            $separators = [','];
            $fragments  = $this->explodeExpression($itemsExpression, $separators);
            $items      = array_diff($fragments, $separators);
            $values     = array_map([$this->itemParser, 'parse'], $items);
            
            return array(
                'type'  => 'array',
                'value' => array_values($values),
            );
        }
        
        throw new ParserException($rawExpression);
    }
}
