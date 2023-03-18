<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\OperationCommand;
use FormulaInterpreter\Command\CommandInterface;
use FormulaInterpreter\Command\CommandFactory\OperationCommandFactory;

/**
 * Description of OperationCommandFactory
 *
 * @author mathieu
 */
class OperationCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $this->factory = new OperationCommandFactory($this->createCommandFactoryMock());
    }
    
    /**
     * 
     */
    public function testCreateWithOneOperand() {
        $options = array(
            'firstOperand' => array(2),
        );
        $this->assertEquals($this->factory->create($options), new OperationCommand(new OperationCommandFactoryTest_FakeCommand(array(2))));
    }
    
    /**
     * 
     */
    public function testCreateWithEmptyOthersOperands() {
        $options = array(
            'firstOperand' => array(2),
            'otherOperands' => array(
                array(),
            ),
        );
        $this->assertEquals($this->factory->create($options), new OperationCommand(new OperationCommandFactoryTest_FakeCommand(array(2))));
    }

    
    /**
     * 
     */

     public function testCreateWithTwoOperands() { 
        $options = array(
            'firstOperand' => array(2),
            'otherOperands' => array(
                array('operator' => 'add', 'value' =>  array('3'))
            )
        );
        
        $expected = new OperationCommand(new OperationCommandFactoryTest_FakeCommand(array(2)));
        $expected->addOperand('add', new OperationCommandFactoryTest_FakeCommand(array(3)));
        
        $this->assertEquals($this->factory->create($options), $expected);
    }
        
    /**
     * @expectedException FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingFirstOperandOption() {
        $this->factory->create(array());
    }
    
    protected function createCommandFactoryMock() {
        
        $operandFactory = $this->getMock('FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface');
        $operandFactory->expects($this->any())
                ->method('create')
                ->will($this->returnCallback('OperationCommandFactoryTest::createFakeCommand'));
        return $operandFactory;
        
    }

    static function createFakeCommand($options) {
        return new OperationCommandFactoryTest_FakeCommand($options);
    }
    
}

class OperationCommandFactoryTest_FakeCommand implements CommandInterface {
    
    protected $options;
    
    function __construct($options) {
        $this->options = $options;
    }
    
    public function run() {}
}
