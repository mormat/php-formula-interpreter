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
            array('1+3', array(
                'firstOperand' => '1',
                'otherOperands' => array(
                    array('operator' => 'add', 'value' => '3')
                )
            )),
            
            array('3+1-2', array(
                'firstOperand' => '3',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => '1'),
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
                    array('operator' => 'divide',   'value' => '2'),
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
                    array('operator' => 'add',      'value' => '3+2'),
                )
            )),
            
            array(' ( 3+2 ) ', array(
                'firstOperand' => '3',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => '2'),
                )
            ))
            
        );
    }
    
    public function mockOperandParser($expression) {
        return $expression;
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
    
    /**
     * @dataProvider getSplitExpressionByOperatorsData
     */
    public function testSplitExpressionByOperators($expression, $operators, $expected)
    {
        $results = OperatorParser::splitExpressionByOperators($expression, $operators);
        
        $this->assertJsonStringEqualsJsonString(
            json_encode($results),
            json_encode($expected),
            sprintf("actual value is %s", json_encode($results))
        );
        
    }
    
    public function getSplitExpressionByOperatorsData() {
        return array(
            
            array(
                '2 + 3 - 4 + 5', 
                ['+', '-'], 
                ['2 ', '+', ' 3 ', '-', " 4 ", '+', ' 5']
            ),
            array(
                '2 * 3 + 4', 
                ['*', '+'], 
                ['2 ', '*', ' 3 ', '+', " 4"]
            ),
            array(
                '2 * (3 + 4)', 
                ['*', '+'], 
                ['2 ', '*', ' (3 + 4)']
            ),
            array(
                "'a' in foo or 'b' in bar", 
                [' in ', ' or '], 
                ["'a'", ' in ', 'foo', ' or ', "'b'", ' in ', 'bar']
            ),
            
            array(
                "((5) * 2) / (3 - 1)", 
                ["+", "-"], 
                ["((5) * 2) / (3 - 1)"]
            ),

            array(
                "'fun' in 'fun in fundamental'", 
                [" in "], 
                ["'fun'", ' in ', "'fun in fundamental'"]
            )
            
        );
    }
    
}
