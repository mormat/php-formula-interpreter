<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\UnaryOperatorParser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UnaryOperatorParserTest extends TestCase
{
    protected UnaryOperatorParser $parser;
    
    protected function setUp(): void
    {
        $childParser = $this->createMock(ParserInterface::class);
        $childParser->method('parse')->willReturnCallback(
            function ($expression) {
                if (str_contains($expression, '#bad_expr')) {
                    throw new ParserException($expression);
                }
                return ['parsed' => $expression];
            }
        );
        
        $this->parser = new UnaryOperatorParser($childParser);
    }
    
    #[DataProvider('dataParseWithValidExpression')]
    public function testParseWithValidExpression(
        $expression,
        $expectedOperator,
        $expectedValue
    ) {
        $this->assertEquals(
            array(
                'type'     => 'unary_operator',
                'value'    => ['parsed' =>  $expectedValue],
                'operator' => $expectedOperator,
            ),
            $this->parser->parse($expression)
        );
    }
    
    public static function dataParseWithValidExpression()
    {
        return array(
            ['not true', 'not', ' true'],
            ['not(true)', 'not', '(true)'],
            ['not[true]', 'not', '[true]']
        );
    }
    
    #[DataProvider('dataParseWithInvalidExpression')]
    public function testParseWithInvalidExpression($expression, $unparsable)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $unparsable)
        );
        
        $this->parser->parse($expression);
    }
    
    public static function dataParseWithInvalidExpression()
    {
        return array(
            [ 'whatever',  'whatever' ],
            [ 'notfoo',    'notfoo' ],
            [ 'not #bad_expr', ' #bad_expr'],
        );
    }
}
