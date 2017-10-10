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

}

