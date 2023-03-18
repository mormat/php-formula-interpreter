<?php

namespace Mormat\FormulaInterpreter\Parser;

/**
 * Description of FunctionParser
 *
 * @author mormat
 */
class CompositeParser implements ParserInterface {
    
    protected $parsers = array();
    
    public function addParser(ParserInterface $parser) {
        $this->parsers[] = $parser;
    }
    
    function parse($expression) {
        foreach ($this->parsers as $parser) {
            try {
                return $parser->parse($expression);
            } catch (ParserException $e) {
                if ($e->getExpression() != $expression) {
                    throw $e;
                }
            }           
        }
        
        throw new ParserException($expression);
    }


}
