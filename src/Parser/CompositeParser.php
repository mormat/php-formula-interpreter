<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Exception\StackOverflowException;

/**
 * Description of CompositeParser
 *
 * @author mormat
 */
class CompositeParser implements ParserInterface {
    
    protected $parsers  = array();
    protected static $nbrCalls = 0;
    
    const NBR_CALLS_MAX = 10000;

    public function addParser(ParserInterface $parser) {
        $this->parsers[] = $parser;
    }
    
    function parse($expression) {
        if (self::$nbrCalls >= self::NBR_CALLS_MAX) {
            throw new StackOverflowException(
                "Too many recursive call when parsing exception"
            );
        }
        
        foreach ($this->parsers as $parser) {
            try {
                self::$nbrCalls++;
                $result = $parser->parse($expression);
                self::$nbrCalls--;
                return $result;
            } catch (ParserException $e) {
                self::$nbrCalls--;
                if ($e->getExpression() != $expression) {
                    throw $e;
                }
            }           
        }
        
        throw new ParserException($expression);
    }


}
