<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\OperationCommand;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class OperationCommandTest extends PHPUnit_Framework_TestCase {
    
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
    
    public function createMockCommand($returnValue) {
        $command = $this->getMock('FormulaInterpreter\Command\CommandInterface');
        $command->expects($this->any())
            ->method('run')
            ->will($this->returnValue($returnValue));
        return $command;
    }

    
}

?>
