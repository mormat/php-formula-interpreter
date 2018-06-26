<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Compiler;
use FormulaInterpreter\Parser\ParserException;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class CompilerTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getCompileAndRunData
     */
    public function testCompileAndRun($expression, $result, $variables = array()) {
        $compiler = new Compiler();

        $executable = $compiler->compile($expression);
        $this->assertEquals($executable->run($variables), $result);

    }

    public function getCompileAndRunData() {
        return array(
            array('3', 3),
            array('3 + 3', 6),
            array('price', 10, array('price' => 10)),
            array('price + 2 * 3', 16, array('price' => 10)),
            array('pi()', pi()),
            array('pow(3, 2)', 9),
            array('modulo(5, 2)', 1),
            array('cos(0)', 1),
            array('sin(0)', 0),
            array('sqrt(4)', 2),
            array('pow(sqrt(pow(2, 2)), 2)', 4),

            // Issue #4
            array('(((100 * 0.43075) * 1.1 * 1.5) / (1-0.425)) * 1.105', 136.5852065217),
            array('1+(1+1)', 3),

            // Issue 8
            array('pow(foo,bar)', 9, array('foo' => 3, 'bar' => 2)),
            array('pow(foo, bar)', 9, array('foo' => 3, 'bar' => 2)),
        );
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
        return array(
            array('3', array()),
            array('3 + 3', array()),
            array('price', array('price')),
            array('price + 2 * 3', array('price')),
            array('pi()', array()),
            array('pow(3, 2)', array()),
            array('modulo(5, 2)', array()),
            array('cos(0)', array()),
            array('sin(0)', array()),
            array('sqrt(foo)', array('foo')),
            array('foo', array('foo')),
            array('foo + 1', array('foo')),
            array('foo * bar', array('foo', 'bar')),
            array('pow(foo, bar)', array('foo', 'bar')),
            array('pow(sqrt(pow(foo, bar)), baz)', array('foo', 'bar', 'baz')),
        );
    }
}
