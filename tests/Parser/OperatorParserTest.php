<?php

use Mormat\FormulaInterpreter\Parser\OperatorParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parsing of operators
 * 
 * @author mormat
 */
class OperatorParserTest extends TestCase {
    
    protected OperatorParser $parser;
    
    public function setUp(): void {
        
        $operandParser = $this->getMockBuilder(
            ParserInterface::class
        )->getMock();
        $operandParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockOperandParser')));
        
        $this->parser = new OperatorParser($operandParser);
    }
    
    #[DataProvider('getParseWithValidExpressionData')]
    public function testParseWithValidExpression($expression, $infos) {
        $infos['type'] = 'operation';
        
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    /**
     * @todo make this dataProvider more readable
     * 
     * @return array
     */
    public static function getParseWithValidExpressionData() {
        
        
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
                    array('operator' => 'multiply', 'value' => 'operand (3+2)'),
                )
            )),
            
            array('4* (3+2) ', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'multiply', 'value' => 'operand (3+2)'),
                )
            )),
            
            array('4+( 3+2 ) ', array(
                'firstOperand' => 'operand 4',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => 'operand ( 3+2 )'),
                )
            )),
            
            array(' ( 3+2 ) ', array(
                'firstOperand' => 'operand 3',
                'otherOperands' => array(
                    array('operator' => 'add',      'value' => 'operand 2'),
                )
            )),
            
            array(' 1 in [1, 2] ', array(
                'firstOperand' => 'operand 1 ',
                'otherOperands' => array(
                    array('operator' => 'in',      'value' => 'operand [1, 2]'),
                )
            )),
            
            // checking priorities of the 'in' operator
            array(' 1+2 in [3,4]', array(
                'firstOperand' => 'operand 1+2 ',
                'otherOperands' => array(
                    array('operator' => 'in', 'value' => 'operand [3,4]'),
                )
            )),
            
            array(' 2*2 in [4,5] ', array(
                'firstOperand' => 'operand 2*2 ',
                'otherOperands' => array(
                    array('operator' => 'in', 'value' => 'operand [4,5]'),
                )
            )),
            
            // 'lower than' operator
            array(' 2 < 3 ', array(
                'firstOperand' => 'operand 2 ',
                'otherOperands' => array(
                    array('operator' => 'lower', 'value' => 'operand 3'),
                )
            )),
            array(' 2 + 1 < 3 ', array(
                'firstOperand' => 'operand 2 + 1 ',
                'otherOperands' => array(
                    array('operator' => 'lower', 'value' => 'operand 3'),
                )
            )),
            array(' 1 < 2 - 1 ', array(
                'firstOperand' => 'operand 1 ',
                'otherOperands' => array(
                    array('operator' => 'lower', 'value' => 'operand 2 - 1'),
                )
            )),
            
            // 'greater than' operator
            array(' 2 > 3 ', array(
                'firstOperand' => 'operand 2 ',
                'otherOperands' => array(
                    array('operator' => 'greater', 'value' => 'operand 3'),
                )
            )),
            array(' 3 - 1 > 2 ', array(
                'firstOperand' => 'operand 3 - 1 ',
                'otherOperands' => array(
                    array('operator' => 'greater', 'value' => 'operand 2'),
                )
            )),
            array(' 3 > 2 + 1 ', array(
                'firstOperand' => 'operand 3 ',
                'otherOperands' => array(
                    array('operator' => 'greater', 'value' => 'operand 2 + 1'),
                )
            )),
            
            // 'equal' operator
            array(' 2 = 3 ', array(
                'firstOperand' => 'operand 2 ',
                'otherOperands' => array(
                    array('operator' => 'equal', 'value' => 'operand 3'),
                )
            )),
            array(' 3 + 1 = 3 ', array(
                'firstOperand' => 'operand 3 + 1 ',
                'otherOperands' => array(
                    array('operator' => 'equal', 'value' => 'operand 3'),
                )
            )),
            array(' 3 = 3 + 1 ', array(
                'firstOperand' => 'operand 3 ',
                'otherOperands' => array(
                    array('operator' => 'equal', 'value' => 'operand 3 + 1'),
                )
            )),
            
            
            // 'lower or equal' operator
            array(' 2 <= 3 ', array(
                'firstOperand' => 'operand 2 ',
                'otherOperands' => array(
                    array('operator' => 'lower_or_equal', 'value' => 'operand 3'),
                )
            )),
            array(' 3 + 1 <= 3 ', array(
                'firstOperand' => 'operand 3 + 1 ',
                'otherOperands' => array(
                    array('operator' => 'lower_or_equal', 'value' => 'operand 3'),
                )
            )),
            array(' 3 <= 3 - 1 ', array(
                'firstOperand' => 'operand 3 ',
                'otherOperands' => array(
                    array('operator' => 'lower_or_equal', 'value' => 'operand 3 - 1'),
                )
            )),
            
            // 'greater than' operator
            array(' 2 >= 3 ', array(
                'firstOperand' => 'operand 2 ',
                'otherOperands' => array(
                    array('operator' => 'greater_or_equal', 'value' => 'operand 3'),
                )
            )),
            array(' 3 >= 3 + 1 ', array(
                'firstOperand' => 'operand 3 ',
                'otherOperands' => array(
                    array('operator' => 'greater_or_equal', 'value' => 'operand 3 + 1'),
                )
            )),
            array(' 3 >= 3 + 1 ', array(
                'firstOperand' => 'operand 3 ',
                'otherOperands' => array(
                    array('operator' => 'greater_or_equal', 'value' => 'operand 3 + 1'),
                )
            )),
                        
        );
    }
    
    public function mockOperandParser($expression) {
        return 'operand ' . $expression;
    }

    #[DataProvider('getUncorrectExpressions')]
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        
        $this->parser->parse($expression);
    }
    
    public static function getUncorrectExpressions() {
        return array(
            array(' what ever '),
            array('2 + '),
            // array(' 2 + ()'),
            array(' ( 2 + )')
        );
    }
    
}

