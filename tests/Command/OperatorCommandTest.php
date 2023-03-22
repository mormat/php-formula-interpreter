<?php

use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\OperationCommand;
use Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

/**
 * Tests the execution of the operators
 *
 * @author mormat
 */
class OperatorCommandTest extends PHPUnit_Framework_TestCase {
    
    /**
     *
     */
    public function testRunWithOneOperand() {
    
        $firstOperand = $this->createMockCommand(12);
        
        $command = new OperationCommand($firstOperand);
        
        $this->assertEquals($command->run(), 12);
    }
    
    /**
     *
     */
    public function testRunWithAddition() {
    
        $firstOperand = $this->createMockCommand(2);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand(2);
        $command->addOperand(OperationCommand::ADD_OPERATOR, $operand);
        
        $this->assertEquals($command->run(), 4);
    }
    
    /**
     *
     */
    public function testRunWithMultplication() {
    
        $firstOperand = $this->createMockCommand(3);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand(2);
        $command->addOperand(OperationCommand::MULTIPLY_OPERATOR, $operand);
        
        $this->assertEquals($command->run(), 6);
    }
    
    /**
     *
     */
    public function testRunWithSubstraction() {
    
        $firstOperand = $this->createMockCommand(2);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand(1);
        $command->addOperand(OperationCommand::SUBTRACT_OPERATOR, $operand);
        
        $this->assertEquals($command->run(), 1);
    }
    
    /**
     *
     */
    public function testRunWithDivision() {
    
        $firstOperand = $this->createMockCommand(6);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand(2);
        $command->addOperand(OperationCommand::DIVIDE_OPERATOR, $operand);
        
        $this->assertEquals($command->run(), 3);
        
    }
    
    /**
     * @dataProvider getRunWithBinaryOperatorData
     */
    public function testRunWithBinaryOperator($operator, $leftValue, $rightValue, $expected) {
    
        // testing with integer
        $firstOperand = $this->createMockCommand($leftValue);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand($rightValue);
        $command->addOperand($operator, $operand);
        
        $this->assertEquals($command->run(), $expected);
    }
    
    public function getRunWithBinaryOperatorData()
    {
        return array(
            array('in', 2,     [1, 2, 3], true),
            array('in', 6,     [1, 2, 3], false),
            array('in', 'foo', 'foobar',  true),
            array('in', 'baz', 'foobar',  false),
            
            array('lower', 1, 2, true),
            array('lower', 1, 1, false),
            array('greater', 3, 2, true),
            array('greater', 2, 2, false),
            array('equal', 2, 2, true),
            array('equal', 3, 2, false),
            array('lower_or_equal', 1, 2, true),
            array('lower_or_equal', 2, 2, true),
            array('lower_or_equal', 3, 2, false),
            array('greater_or_equal', 3, 2, true),
            array('greater_or_equal', 2, 2, true),
            array('greater_or_equal', 1, 2, false),
        );
    }
    
    /**
     *
     */
    public function testRunWithThreeOperands() {
    
        $firstOperand = $this->createMockCommand(5);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand(10);
        $command->addOperand(OperationCommand::ADD_OPERATOR, $operand);
        
        $operand = $this->createMockCommand(1);
        $command->addOperand(OperationCommand::SUBTRACT_OPERATOR, $operand);
        
        $this->assertEquals($command->run(), 14);
    }
    
    /**
     * @dataProvider getRunWithInvalidOperandsData
     */
    public function testRunWithInvalidOperands($firstValue, $secondValue, $operator)
    {
        $exception = UnsupportedOperandTypeException::class;
        $this->expectException($exception);
        
        $firstOperand = $this->createMockCommand($firstValue);
        $command = new OperationCommand($firstOperand);
        
        $operand = $this->createMockCommand($secondValue);
        $command->addOperand(OperationCommand::DIVIDE_OPERATOR, $operand);
        
        $command->run();
    }
    
    public function getRunWithInvalidOperandsData()
    {
        return array(
            // additions
            [ 'foo', 'bar', OperationCommand::ADD_OPERATOR    ],
            [ 2,     'foo', OperationCommand::ADD_OPERATOR    ],
            ['foo',  2,     OperationCommand::ADD_OPERATOR    ],
            
            // substractions
            [ 'foo', 'bar', OperationCommand::SUBTRACT_OPERATOR ],
            [ 2,     'foo', OperationCommand::SUBTRACT_OPERATOR ],
            ['foo',  2,     OperationCommand::SUBTRACT_OPERATOR ],
            
            // multiplications
            [ 'foo', 'bar', OperationCommand::MULTIPLY_OPERATOR ],
            [ 2,     'foo', OperationCommand::MULTIPLY_OPERATOR ],
            ['foo',  2,     OperationCommand::MULTIPLY_OPERATOR ],
            
            // divisions
            [ 'foo', 'bar', OperationCommand::DIVIDE_OPERATOR ],
            [ 2,     'foo', OperationCommand::DIVIDE_OPERATOR ],
            ['foo',  2,     OperationCommand::DIVIDE_OPERATOR ],
        );
    }
    
    // @rename to createMockCommandReturningValue
    public function createMockCommand($returnValue) {
        $command = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $command->expects($this->any())
            ->method('run')
            ->will($this->returnValue($returnValue));
        return $command;
    }
   
}
