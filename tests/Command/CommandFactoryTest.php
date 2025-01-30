<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;

use PHPUnit\Framework\TestCase;

class CommandFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = new CommandFactory();
        
        $command = $this->createFakeCommand();
        $numericFactory = $this->getMockBuilder(
            CommandFactory::class
        )->getMock();
        $numericFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($command));
        $factory->registerFactory('numeric', $numericFactory);
        
        $this->assertEquals($factory->create(array('type' => 'numeric')), $command);
    }
    
    public function testMissingTypeOption()
    {
        $this->expectException(CommandFactory\CommandFactoryException::class);
        
        $factory = new CommandFactory();
                
        $factory->create(array());
    }
    
    public function testUnknownType()
    {
        $this->expectException(CommandFactory\CommandFactoryException::class);
        
        $factory = new CommandFactory();
                
        $factory->create(array('type' => 'numeric'));
    }
    
    protected function createFakeCommand()
    {
        return new class implements CommandInterface
        {
            public function run(CommandContext $context)
            {
            }
        };
    }
}
