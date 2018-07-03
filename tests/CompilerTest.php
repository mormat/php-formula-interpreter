<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter;

use FormulaInterpreter\Compiler;
use FormulaInterpreter\Parser\ParserException;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class CompilerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider getCompileAndRunData
     */
    public function testCompileAndRun($expression, $result, $variables = [])
    {
        $compiler = new Compiler();

        $executable = $compiler->compile($expression);
        $this->assertEquals($executable->run($variables), $result);
    }

    public function getCompileAndRunData()
    {
        return [
            ['3', 3],
            ['3 + 3', 6],
            ['price', 10, ['price' => 10]],
            ['price + 2 * 3', 16, ['price' => 10]],
            ['pi()', pi()],
            ['pow(3, 2)', 9],
            ['modulo(5, 2)', 1],
            ['cos(0)', 1],
            ['sin(0)', 0],
            ['sqrt(4)', 2],
            ['pow(sqrt(pow(2, 2)), 2)', 4],

            // Issue #4
            ['(((100 * 0.43075) * 1.1 * 1.5) / (1-0.425)) * 1.105', 136.5852065217],
            ['1+(1+1)', 3],

            // Issue 8
            ['pow(foo,bar)', 9, ['foo' => 3, 'bar' => 2]],
            ['pow(foo, bar)', 9, ['foo' => 3, 'bar' => 2]],

            // Support dot
            ['pow(foo.bar, bar.foo)', 9, ['foo.bar' => 3, 'bar.foo' => 2]],

            // Support comparison
            ['3 = 3', true],
            ['3 = 4', false],
            ['3 <> 3', false],
            ['3 != 4', true],
            ['3 != 3', false],
            ['3 <> 4', true],
            ['3 > 3', false],
            ['4 > 3', true],
            ['3 >= 3', true],
            ['3 < 3', false],
            ['3 <= 3', true],
            ['3 < 4', true],

            //Support boolean and, or
            ['(3 = 3) AND (4 > 1)', true],
            ['(3 = 3) AND (4 < 1)', false],
            ['(3 = 3) OR (4 < 1)', true],
            ['(3 < 3) OR (4 < 1)', false],

            //Support string
            ['a = "Hello"', true, ['a' => "Hello"]],
            ['a = "Hello"', false, ['a' => "World"]],
            ['a = "Hello - World"', true, ['a' => "Hello - World"]],
            ['(a = "Hello") AND (b = "Hello World")', false, ['a' => "World", 'b' => "Hello World"]],
            ['(a = "Hello") AND (b = "Hello - World")', false, ['a' => "World", 'b' => "Hello - World"]],
            ['(a = "Hello") AND (b = "Hello - World")', true, ['a' => "Hello", 'b' => "Hello - World"]],
        ];
    }

    /**
     * @dataProvider getCompileGetParametersData
     */
    public function testCompileGetParameters($expression, $parameters)
    {
        $compiler = new Compiler();

        $executable = $compiler->compile($expression);

        $this->assertSame($executable->getParameters(), $parameters);
    }

    public function getCompileGetParametersData()
    {
        return [
            ['3', []],
            ['3 + 3', []],
            ['price', ['price']],
            ['price + 2 * 3', ['price']],
            ['pi()', []],
            ['pow(3, 2)', []],
            ['modulo(5, 2)', []],
            ['cos(0)', []],
            ['sin(0)', []],
            ['sqrt(foo)', ['foo']],
            ['foo', ['foo']],
            ['foo + 1', ['foo']],
            ['foo * bar', ['foo', 'bar']],
            ['pow(foo, bar)', ['foo', 'bar']],
            ['pow(sqrt(pow(foo, bar)), baz)', ['foo', 'bar', 'baz']],
            ['foo.bar * bar.foo', ['foo.bar', 'bar.foo']],
        ];
    }

    /**
     * @dataProvider getCompileRegisterFunctionsData
     */
    public function testCompileRegisterFunctions($expression, $result, $variables = [], $functions = [])
    {
        $compiler = new Compiler();

        foreach ($functions as $name => $callable) {
            $compiler->registerFunction($name, $callable);
        }

        $executable = $compiler->compile($expression);
        $this->assertEquals($executable->run($variables), $result);
    }

    public function getCompileRegisterFunctionsData()
    {
        return [
            ['max(foo.bar, bar.foo)', 3, ['foo.bar' => 3, 'bar.foo' => 2], ['max' => 'max']],
            ['foobar(foo.bar, bar.foo)', 6, ['foo.bar' => 3, 'bar.foo' => 2], [
                'foobar' => function($a, $b) {
                    return $a * $b;
                }
            ]],
            ['foobar1(foobar2(foo.bar), bar.foo)', 12, ['foo.bar' => 3, 'bar.foo' => 2], [
                'foobar1' => function($a, $b) {
                    return $a * $b;
                },
                'foobar2' => function($a) {
                    return $a * 2;
                }
            ]],
        ];

    }
}
