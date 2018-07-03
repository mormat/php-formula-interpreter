<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Parser;

use FormulaInterpreter\Parser\FunctionParser;
use FormulaInterpreter\Parser\ParserException;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class FunctionParserTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $argumentParser = $this->createMock('\FormulaInterpreter\Parser\ParserInterface');
        $argumentParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback([$this, 'mockArgumentParser']));
        $this->parser = new FunctionParser($argumentParser);
    }

    /**
     * @dataProvider getCorrectExpressions
     */
    public function testParseWithCorrecrExpression($expression, $infos)
    {
        $infos['type'] = 'function';

        $this->assertEquals($this->parser->parse($expression), $infos);
    }

    public function getCorrectExpressions()
    {
        return [
            ['pi()', ['name' => 'pi']],
            ['do_this()', ['name' => 'do_this']],
            ['now()', ['name' => 'now']],
            ['sqrt(2)', ['name' => 'sqrt', 'arguments' => ['2']]],
            ['cos(0)', ['name' => 'cos', 'arguments' => ['0']]],
            ['pi(  )', ['name' => 'pi']],
            ['pow(2,3)', ['name' => 'pow', 'arguments' => ['2', '3']]],
            ['sqrt(pi())', ['name' => 'sqrt', 'arguments' => ['pi()']]],
            [' pi() ', ['name' => 'pi']],
            ['max(sqrt(pow(2,4)),2)', ['name' => 'max', 'arguments' => ['sqrt(pow(2,4))', '2']]],
        ];
    }

    /**
     * @dataProvider getUncorrectExpressions
     */
    public function testParseUncorrectExpression($expression)
    {
        $this->expectException(ParserException::class);
        $this->parser->parse($expression);
    }

    public function getUncorrectExpressions()
    {
        return [
            [' what ever '],
            [' what_ever( '],
        ];
    }

    public function mockArgumentParser($expression)
    {
        return $expression;
    }
}
