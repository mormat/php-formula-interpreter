<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\ArrayCommand;
use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the execution of a array expression
 */
class ArrayCommandTest extends TestCase
{
    protected CommandContext $commandContext;
    
    protected function setUp(): void
    {
        $this->commandContext = new CommandContext();
    }

    #[DataProvider('getData')]
    public function testRun($items, $result)
    {
        $mocker = [$this, 'createMockCommandReturningValue'];
        
        $command = new ArrayCommand(
            array_map($mocker, $items)
        );
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public static function getData()
    {
        return array(
            array([1, 2], ['mocking 1', 'mocking 2']),
        );
    }
    
    public function createMockCommandReturningValue($value)
    {
        $command = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $command->expects($this->any())
            ->method('run')
            ->will($this->returnValue('mocking '.$value));
        return $command;
    }
}
