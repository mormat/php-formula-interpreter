<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Parser\OperatorParser;

/**
 * Description of OperatorParserTest
 *
 * @author mathieu
 */
class OperatorParserTest extends PHPUnit_Framework_TestCase {

    public function setUp() {

        $operandParser = $this->getMock('\FormulaInterpreter\Parser\ParserInterface');
        $operandParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockOperandParser')));

        $this->parser = new OperatorParser($operandParser);
    }

    /**
     * @dataProvider getDataForTestingParse
     */
    public function testParse($expression, $infos) {
        $infos['type'] = 'operation';

        $this->assertEquals($this->parser->parse($expression), $infos);
    }

    public function getDataForTestingParse() {

        return array(
            array('2+2', array(
                            'firstOperand' => '2',
                            'otherOperands' => array(
                                array('operator' => 'add', 'value' => '2')
                             )
                         )),
            array(' 2+2 ', array(
                            'firstOperand' => '2',
                            'otherOperands' => array(
                                array('operator' => 'add', 'value' => '2')
                             )
                         )),
            array('2-2', array(
                            'firstOperand' => '2',
                            'otherOperands' => array(
                                array('operator' => 'subtract', 'value' => '2')
                            )
                         )),
            array('3+1-2', array(
                            'firstOperand' => '3',
                            'otherOperands' => array(
                                array('operator' => 'add', 'value' => '1'),
                                array('operator' => 'subtract', 'value' => '2')
                            )
                         )),
            array('2*2', array(
                            'firstOperand' => '2',
                            'otherOperands' => array(
                                array('operator' => 'multiply', 'value' => '2'),
                            )
                         )),
            array('2+3*4', array(
                            'firstOperand' => '2',
                            'otherOperands' => array(
                                array('operator' => 'add', 'value' => '3*4'),
                             )
                         )),
            array('4*3/2', array(
                            'firstOperand' => '4',
                            'otherOperands' => array(
                                array('operator' => 'multiply', 'value' => '3'),
                                array('operator' => 'divide', 'value' => '2'),
                            )
                         )),
            array('4*(3+2)', array(
                            'firstOperand' => '4',
                            'otherOperands' => array(
                                array('operator' => 'multiply', 'value' => '3+2'),
                            )
                         )),
            array('4* (3+2) ', array(
                            'firstOperand' => '4',
                            'otherOperands' => array(
                                array('operator' => 'multiply', 'value' => '3+2'),
                            )
                         )),
            array('4+( 3+2 ) ', array(
                            'firstOperand' => '4',
                            'otherOperands' => array(
                                array('operator' => 'add', 'value' => '3+2'),
                            )
                         )),
        );
    }

    public function mockOperandParser($expression) {
        return $expression;
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
            array('2 + '),
            array(' 2 + ()')
        );
    }

}

?>
