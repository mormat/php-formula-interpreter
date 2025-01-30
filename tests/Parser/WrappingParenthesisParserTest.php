<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\WrappingParenthesisParser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WrappingParenthesisParserTest extends TestCase
{
    protected WrappingParenthesisParser $parser;
    
    protected function setUp(): void
    {
        $childParser = $this->createMock(ParserInterface::class);
        $childParser->method('parse')->willReturnCallback(function ($expr) {
            if ($expr === '') {
                throw new ParserException($expr);
            }
            return ['parsed' => $expr];
        });
        
        $this->parser = new WrappingParenthesisParser($childParser);
    }
    
    #[DataProvider('dataParse')]
    public function testParse($expression, $expectedParsing)
    {
        $this->assertEquals(
            ['parsed' => $expectedParsing],
            $this->parser->parse($expression)
        );
    }
    
    public static function dataParse()
    {
        return [
            ['(0)', '0'],
            ['(100)', '100'],
            ['((100) + (100))', '(100) + (100)'],
        ];
    }
    
    #[DataProvider('dataParseWithInvalidExpression')]
    public function testParseWithInvalidExpression($invalidExpression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $invalidExpression)
        );
        
        $this->parser->parse($invalidExpression);
    }
    
    public static function dataParseWithInvalidExpression()
    {
        return [
            ['whatever'],
            ['()'],
            [' (2) '],
            ['(100) + (100)']
        ];
    }
}
