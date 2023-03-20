<?php

use Mormat\FormulaInterpreter\Parser\OperatorParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

/**
 * Tests the parsing of operators
 * 
 * @author mormat
 */
class OperatorParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
        $operandParser = $this->getMockBuilder(
            ParserInterface::class
        )->getMock();
        $operandParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockOperandParser')));
        
        $this->parser = new OperatorParser($operandParser);
    }
    
    /**
     * @dataProvider getParseWithValidExpressionData
     */
    public function testParseWithValidExpression($expression, $infos) {
        $infos['type'] = 'operation';
        
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getParseWithValidExpressionData() {
        
        return array(
            array('2+2', array(
                'firstOperand' => 'operand 2',
                'otherOperands' => array(
                    array('operator' => 'add', 'value' => 'operand 2')
                 )
            )), 
            array(' 2+2 ', array(
                'firstOperand' => 'operand 2',
                'otherOperands' => array(
                    array('operator' => 'add', 'value' => 'operand 2')
                 )
            )),
            array('2-2', array(
                'firstOperand' => 'operand 2',
                'otherOperands' => array(
                    array('operator' => 'subtract', 'value' => 'operand 2')
                )
            )),
            array('1+3', array(
                'firstOperand' => 'operand 1',
                'otherOperands' => array(
                    array('operator' => 'add', 'value' => 'operand 3')
                )
            )),
            
            array('3+1-2', array(
                'firstOperand' => 'operand 3',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => 'operand 1'),
                    array('operator' => 'subtract', 'value' => 'operand 2')
                )
            )),
            
            array('2*2', array(
                'firstOperand' => 'operand 2',
                'otherOperands' => array(
                    array('operator' => 'multiply', 'value' => 'operand 2'),
                )
            )),
            
            array('2+3*4', array(
                'firstOperand' => 'operand 2',
                'otherOperands' => array(
                    array('operator' => 'add', 'value' => 'operand 3*4'),
                 )
            )),
            
            array('4*3/2', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'multiply', 'value' => 'operand 3'),
                    array('operator' => 'divide',   'value' => 'operand 2'),
                )
            )),
            
            array('4*(3+2)', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'multiply', 'value' => 'operand 3+2'),
                )
            )),
            
            array('4* (3+2) ', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'multiply', 'value' => 'operand 3+2'),
                )
            )),
            
            array('4+( 3+2 ) ', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => 'operand 3+2'),
                )
            )),
            
            array(' ( 3+2 ) ', array(
                'firstOperand' => 'operand 3',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => 'operand 2'),
                )
            ))
            
        );
    }
    
    public function mockOperandParser($expression) {
        return 'operand ' . $expression;
    }

    /**
     * @dataProvider getUncorrectExpressions
     */
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressions() {
        return array(
            array(' what ever '),
            array('2 + '),
            array(' 2 + ()'),
            array(' ( 2 + )')
        );
    }
    
}

/*
class OperatorParserTest_OperatorParser extends OperatorParser
{
    function explodeExpression($expression, array $separators, array $options = [])
    {
        return ExpressionExploderTraitTest::mockExplodeExpression($expression, $options);
    }
}
 
 */
