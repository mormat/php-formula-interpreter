<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Parser;

use FormulaInterpreter\Parser\VariableParser;
use FormulaInterpreter\Parser\ParserException;

/**
 * Description of NumericParserTest
 *
 * @author mathieu
 */
class VariableParserTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->parser = new VariableParser();
    }

    /**
     * @dataProvider getCorrectExpressions
     */
    public function testParseCorrectExpression($expression, $infos)
    {
        $infos['type'] = 'variable';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getCorrectExpressions()
    {
        return [
            ['price', ['name' => 'price']],
            ['rate', ['name' => 'rate']],
            [' rate ', ['name' => 'rate']],
            ['with_underscore', ['name' => 'with_underscore']],
            ['camelCase', ['name' => 'camelCase']],
            ['rate2', ['name' => 'rate2']],
            ['with.dot', ['name' => 'with.dot']],
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
            [''],
            ['23'],
            ['23 12'],
            [' some_function( '],
        ];
    }
}
