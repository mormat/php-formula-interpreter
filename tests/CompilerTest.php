<?php

use \Mormat\FormulaInterpreter\Compiler;
use \Mormat\FormulaInterpreter\Functions\CallableFunction;
use \Mormat\FormulaInterpreter\Exception\UnknownFunctionException;

/**
 * Tests the compiler
 *
 * @author mormat
 */
class CompilerTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider getCompileAndRunData
     */
    public function testCompileAndRun($expression, $result, $variables = []) {
        $compiler = new Compiler();
        $compiler->registerCustomFunctions([
            new CallableFunction('get_integer_part', 'floor', ['numeric'])
        ]);
        
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
            array('get_integer_part(3.4)', 3, []),
            array('modulo(5, two)', 1, ['two' => 2]),
            
            // Issue #4
            array('(((100 * 0.43075) * 1.1 * 1.5) / (1-0.425)) * 1.105', 136.5852065217), 
            array('1+(1+1)', 3),
            
            // handling strings
            array("'foobar'", 'foobar'),
            array("strtolower('FOOBAR')", 'foobar'),
            array("strtoupper('foobar')", 'FOOBAR'),
            array("ucfirst('foobar')", 'Foobar'),
            array("concat('foo', 'bar')", 'foobar'),
            array("'2 * 3'", '2 * 3'),
            array("strlen('2 + 2') + 1", 6)
            // array("count('foobar')", 6, [], ['count' => 'strlen'])
        );
    }
    
    /**
     * @dataProvider getCompileInvalidExpressionData
     */
    public function testCompileInvalidExpressions($expression, $expectedException)
    {
        $this->expectException($expectedException);
        
        $compiler = new Compiler();
        $executable = $compiler->compile($expression);
        $executable->run();
    }
    
    function getCompileInvalidExpressionData() 
    {
        return array(
            array('get_integer_part(2)', UnknownFunctionException::class),
        );
    }
        
}

