<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;

/**
 * Description of ParserTest
 *
 * @author mormat
 */
class CommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    public function testCreate() {
        $factory = new CommandFactory();
        
        $command = new CommandFactoryTest_FakeCommand();
        $numericFactory = $this->getMockBuilder(
            CommandFactory::class
        )->getMock();
        $numericFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($command));
        $factory->registerFactory('numeric', $numericFactory);
        
        $this->assertEquals($factory->create(array('type' => 'numeric')), $command);
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testMissingTypeOption() {
        $factory = new CommandFactory();
                
        $factory->create(array());
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testUnknownType() {
        $factory = new CommandFactory();
                
        $factory->create(array('type' => 'numeric'));
    }
    
}

class CommandFactoryTest_FakeCommand implements CommandInterface {
    public function run(CommandContext $context) {}
}

