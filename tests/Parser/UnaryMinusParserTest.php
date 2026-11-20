<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\UnaryMinusParser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UnaryMinusParserTest extends TestCase
{
    protected UnaryMinusParser $parser;

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

        $this->parser = new UnaryMinusParser($childParser);
    }

    #[DataProvider('dataParseWithValidExpression')]
    public function testParseWithValidExpression($expression, $expectedValue)
    {
        $this->assertEquals(
            array(
                'type'     => 'unary_operator',
                'value'    => ['parsed' => $expectedValue],
                'operator' => '-',
            ),
            $this->parser->parse($expression)
        );
    }

    public static function dataParseWithValidExpression()
    {
        return array(
            ['-2', '2'],
            ['-x', 'x'],
            ['- x', ' x'],
            ['-(1 + 2)', '(1 + 2)'],
            ['--2', '-2'],
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
            ['2', '2'],
            ['-', '-'],
            ['- ', '- '],
            ['-#bad_expr', '#bad_expr'],
        );
    }
}
