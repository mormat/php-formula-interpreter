<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Parser;

use FormulaInterpreter\Command\OperationCommand;
use FormulaInterpreter\Parser\OperatorParser;
use FormulaInterpreter\Parser\ParserException;
use FormulaInterpreter\Parser\ParserInterface;

/**
 * Description of OperatorParserTest
 *
 * @author mathieu
 */
class OperatorParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var OperatorParser */
    private $parser;

    public function setUp()
    {
        $operandParser = $this->createMock(ParserInterface::class);
        $operandParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback([$this, 'mockOperandParser']));

        $this->parser = new OperatorParser($operandParser);
    }

    /**
     * @dataProvider getDataForTestingParse
     */
    public function testParse($expression, $infos)
    {
        $infos['type'] = 'operation';

        $this->assertEquals($this->parser->parse($expression), $infos);
    }

    public function getDataForTestingParse()
    {
        return [
            ['2+2', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '2']
                ]
            ]],
            [' 2+2 ', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '2']
                ]
            ]],
            ['2-2', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'subtract', 'value' => '2']
                ]
            ]],
            ['2=2', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'equal', 'value' => '2']
                ]
            ]],
            ['3+1-2', [
                'firstOperand' => '3',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '1'],
                    ['operator' => 'subtract', 'value' => '2']
                ]
            ]],
            ['2*2', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'multiply', 'value' => '2'],
                ]
            ]],
            ['2+3*4', [
                'firstOperand' => '2',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '3*4'],
                ]
            ]],
            ['4*3/2', [
                'firstOperand' => '4',
                'otherOperands' => [
                    ['operator' => 'multiply', 'value' => '3'],
                    ['operator' => 'divide', 'value' => '2'],
                ]
            ]],
            ['4*(3+2)', [
                'firstOperand' => '4',
                'otherOperands' => [
                    ['operator' => 'multiply', 'value' => '3+2'],
                ]
            ]],
            ['4* (3+2) ', [
                'firstOperand' => '4',
                'otherOperands' => [
                    ['operator' => 'multiply', 'value' => '3+2'],
                ]
            ]],
            ['4+( 3+2 ) ', [
                'firstOperand' => '4',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '3+2'],
                ]
            ]],
            ['(4+ 3)+ 2 ', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '+2'],
                ]
            ]],
            ['(4+ 3)+ (2*3) ', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '2*3'],
                ]
            ]],
            ['(4+ 3)+ (2*3) - 4', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'add', 'value' => '2*3'],
                    ['operator' => 'subtract', 'value' => '4'],
                ]
            ]],
            ['(4+ 3) = (2*3)', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'equal', 'value' => '2*3'],
                ]
            ]],
            ['(4+ 3) >= (2*3)', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'greater_than_or_equal', 'value' => '2*3'],
                ]
            ]],
            ['(4+ 3) <= (2*3)', [
                'firstOperand' => '4+ 3',
                'otherOperands' => [
                    ['operator' => 'less_than_or_equal', 'value' => '2*3'],
                ]
            ]],
        ];
    }

    public function mockOperandParser($expression)
    {
        return $expression;
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
            ['2 + '],
            [' 2 + ()']
        ];
    }


    /**
     * @dataProvider getDataForTestSearchOperands
     */
    public function testSearchOperands($expression, $operands, $expectedFirstOperand, $expectedOtherOperandOperators)
    {
        $result = $this->parser->searchOperands($expression, $operands);
        $otherOperandOperators = $result['otherOperands'][0]['operator'] ?? null;
        $this->assertEquals($expectedFirstOperand, $result['firstOperand']);
        $this->assertEquals($expectedOtherOperandOperators, $otherOperandOperators);
    }

    public function getDataForTestSearchOperands()
    {
        return [
            ['1 + 1', ['+', '-'], '1', OperationCommand::ADD_OPERATOR],
            ['1 * 1', ['+', '-'], '1 * 1', null],
            ['1 = 1', ['=', '>='], '1', OperationCommand::EQUAL_OPERATOR],
            ['1 >= 1', ['=', '>', '>='], '1', OperationCommand::GREATER_THAN_OR_EQUAL_OPERATOR],
            ['1 > 1', ['=', '>', '>='], '1', OperationCommand::GREATER_THAN_OPERATOR],
        ];
    }

    /**
     * @dataProvider getDataForTestCatchOperatorFromPosition
     */
    public function testCatchOperatorFromPosition($expression, $position, $operators, $expected)
    {
        $result = OperatorParser::catchOperatorFromPosition($expression, $position, $operators);
        $this->assertEquals($expected, $result);
    }

    public function getDataForTestCatchOperatorFromPosition()
    {
        return [
            ['1 + 1', 0, ['+', '-'], null],
            ['1 + 1', 1, ['+', '-'], null],
            ['1 + 1', 2, ['+', '-'], '+'],
            ['1 >= 1', 2, ['+', '-'], null],
            ['1 >= 1', 2, ['=', '>='], '>='],
        ];
    }

    /**
     * @dataProvider getDataForTestHasOperator
     */
    public function testHasOperator($expression, $operator, $expected)
    {
        $result = $this->parser->hasOperator($expression, $operator);
        $this->assertEquals($expected, $result);
    }

    public function getDataForTestHasOperator()
    {
        return [
            ['1 + 1', '+', true],
            ['(1 + 1)', '+', false],
            ['1 = 1', '=', true],
            ['1 > 1', '>', true],
            ['1 >= 1', '>', true], //special case
            ['1 >= 1', '<=', false],
        ];
    }
}
