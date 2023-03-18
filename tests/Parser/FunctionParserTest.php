<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Parser\FunctionParser;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class FunctionParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $argumentParser = $this->getMock('\FormulaInterpreter\Parser\ParserInterface');
        $argumentParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockArgumentParser')));
        $this->parser = new FunctionParser($argumentParser);
        
    }
    
    /**
     * @dataProvider getCorrectExpressions
     */
    public function testParseWithCorrecrExpression($expression, $infos) {
        
        $infos['type'] = 'function';
        
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getCorrectExpressions() {
        return array(
            array('pi()', array('name' => 'pi')),
            array('do_this()', array('name' => 'do_this')),
            array('now()', array('name' => 'now')),
            array('sqrt(2)', array('name' => 'sqrt', 'arguments' => array('2'))),
            array('cos(0)', array('name' => 'cos', 'arguments' => array('0'))),
            array('pi(  )', array('name' => 'pi')),
            array('pow(2,3)', array('name' => 'pow', 'arguments' => array('2', '3'))),
            array('sqrt(pi())', array('name' => 'sqrt', 'arguments' => array('pi()'))),
            array(' pi() ', array('name' => 'pi')),
            array('max(sqrt(pow(2,4)),2)', array('name' => 'max', 'arguments' => array('sqrt(pow(2,4))', '2'))),
        );
    }
    
    /**
     * @expectedException FormulaInterpreter\Parser\ParserException
     * @dataProvider getUncorrectExpressions
     */
    public function testParseUncorrectExpression($expression) {
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressions() {
        return array(
            array(' what ever '),
            array(' what_ever( '),
        );
    }
    
    public function mockArgumentParser($expression) {
        return $expression;
    }
}

?>
