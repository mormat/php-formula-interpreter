<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\ArrayCommand;
use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\ArrayCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;

use PHPUnit\Framework\TestCase;

/**
 * Tests the creation of ArrayCommand
 */
class ArrayCommandFactoryTest extends TestCase
{
    protected CommandFactoryInterface $itemCommandFactoryMock;
    
    public function setUp(): void
    {
        $this->itemCommandFactoryMock = $this->getMockBuilder(
            CommandFactoryInterface::class
        )->getMock();
        $this->itemCommandFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(
                fn($v) => $this->createFakeCommand($v)
            ));
    }
    
    public function testCreate()
    {
        $factory = new ArrayCommandFactory(
            $this->itemCommandFactoryMock
        );
        
        $options = array(
            'value' => [1, 2],
        );
        
        $this->assertEquals(
            $factory->create($options),
            new ArrayCommand([
                $this->createFakeCommand(1),
                $this->createFakeCommand(2),
            ])
        );
    }
    
    protected function createFakeCommand($originalValue): CommandInterface
    {
        return new class($originalValue) implements CommandInterface {
            public function __construct(
                protected $originalValue
            ) {
            }
            public function run($context)
            {
            }
        };
    }
}
