<?php

use Mormat\FormulaInterpreter\Exception\StackOverflowException;
use Mormat\FormulaInterpreter\Parser\CompositeParser;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

use PHPUnit\Framework\TestCase;

/**
 * @author mormat
 */
class CompositeParserTest extends TestCase {
        
    protected int $nbrCalls;
    
    public function setUp(): void
    {
        $this->nbrCalls = 0;
    }
    
    public function testStackOverflowExceptionShouldBeThrownIfTooManyRecursiveCalls()
    {
        $this->expectException(StackOverflowException::class);
        $this->expectExceptionMessage("Too many recursive call when parsing exception");
        
        $parser     = new CompositeParser();
        
        $subParser  = $this->getMockBuilder(ParserInterface::class)->getMock();
        $subParser->expects($this->any())
            ->method('parse')
            ->willReturnCallback(function($expression) use ($parser) {
                $this->nbrCalls++;
                if ($this->nbrCalls < (CompositeParser::NBR_CALLS_MAX + 1)) {
                    $parser->parse($expression);
                }
            });
            
        
        $parser->addParser($subParser);
        $parser->parse('2 + 2');
    }
}
