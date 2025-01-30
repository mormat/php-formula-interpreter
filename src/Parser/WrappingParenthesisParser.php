<?php


namespace Mormat\FormulaInterpreter\Parser;

class WrappingParenthesisParser implements ParserInterface
{

    public function __construct(
        protected ParserInterface $childParser
    ) {
    }

    public function parse($expression)
    {
        if ($expression[0] === '(' && $expression[-1] === ')') {
            $subExpr = substr($expression, 1, -1);
            if ($subExpr !== '' && $this->isParenthesisWellformed($subExpr)) {
                return $this->childParser->parse($subExpr);
            }
        }
        
        throw new ParserException($expression);
    }
    
    public function isParenthesisWellformed($expr): bool
    {
        $openedParenthesis = 0;
        for ($i = 0; $i < strlen($expr); $i++) {
            if ($expr[$i] === '(') {
                $openedParenthesis++;
            }
            if ($expr[$i] === ')') {
                $openedParenthesis--;
            }
            if ($openedParenthesis < 0) {
                return false;
            }
        }
        return $openedParenthesis === 0;
    }
}
