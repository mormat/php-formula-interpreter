<?php

namespace Mormat\FormulaInterpreter\Parser;

use Mormat\FormulaInterpreter\Exception\StackOverflowException;

/**
 * Aggregates multiples parsers into one
 */
class CompositeParser implements ParserInterface
{
    protected $parsers  = array();
    protected static $nbrCalls = 0;
    
    const NBR_CALLS_MAX = 10000;

    public function addParser(ParserInterface $parser)
    {
        $this->parsers[] = $parser;
    }
    
    public function parse($expression)
    {
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

    /**
     * @return ParserInterface[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }
}
