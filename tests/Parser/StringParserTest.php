<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Parser;

use FormulaInterpreter\Parser\NumericParser;
use FormulaInterpreter\Parser\ParserException;
use FormulaInterpreter\Parser\StringParser;
use PHPUnit\Framework\TestCase;

/**
 * Description of StringParserTest
 *
 * @author Petra Barus <petra.barus@gmail.com>
 */
class StringParserTest extends TestCase
{
    /**
     * @var StringParser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new StringParser();
    }
    
    /**
     * @dataProvider getStringValue
     */
    public function testParseInteger($expression, $infos)
    {
        $infos['type'] = 'string';
        $this->assertEquals($infos, $this->parser->parse($expression));
    }
    
    public function getStringValue()
    {
        return [
            ['"Hello"', ['value' => "Hello"]],
            ['"Hello, World!"', ['value' => "Hello, World!"]],
            ['"Hello, \"World!\""', ['value' => 'Hello, "World!"']],
            ['"Hello - World"', ['value' => 'Hello - World']],
        ];
    }
    
    /**
     * @dataProvider getUncorrectExpressionData
     */
    public function testParseUncorrectExpression($expression)
    {
        $this->expectException(ParserException::class);
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressionData()
    {
        return [
            ['mlksdf'],
            ['MLKmlm'],
            [' MLKmlm '],
            [' some_function( '],
            ['2.23.23']
        ];
    }
}
