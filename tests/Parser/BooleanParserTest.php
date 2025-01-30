<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Parser\BooleanParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanParserTest extends TestCase
{
    protected BooleanParser $parser;
    
    protected function setUp(): void
    {
        $this->parser = new BooleanParser();
    }
    
    #[DataProvider('dataParseWithValidValues')]
    public function testParseWithValidValues($expr, $expectedValue)
    {
        $this->assertEquals(
            ['type' => 'boolean', 'value' => $expectedValue],
            $this->parser->parse($expr)
        );
    }
    
    public static function dataParseWithValidValues()
    {
        return array(
            ['true', true],
            ['false', false],
        );
    }

    #[DataProvider('dataParseWithInvalidValues')]
    public function testParseWithInvalidValues($invalidExpression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $invalidExpression)
        );
        
        $this->parser->parse($invalidExpression);
    }
    
    public static function dataParseWithInvalidValues()
    {
        return array(
            [''],
            ['whatever'],
            ['1'],
            ['0'],
            ['tRue'],
            ['fAlse'],
        );
    }
}
