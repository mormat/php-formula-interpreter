<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\OperationCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\OperationCommand;

use PHPUnit\Framework\TestCase;

/**
 * Description of OperationCommandFactory
 *
 * @author mormat
 */
class OperationCommandFactoryTest extends TestCase {
    
    protected OperationCommandFactory $factory;
    
    public function setUp(): void {
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
        
    public function testCreateWithMissingFirstOperandOption() {
        $this->expectException(CommandFactoryException::class);
        $this->factory->create(array());
    }
    
    protected function createCommandFactoryMock() {
        
        $operandFactory = $this->getMockBuilder(
            CommandFactoryInterface::class
        )->getMock();
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
    
    public function run(CommandContext $context) {}
}
