<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\UnaryOperatorCommand;
use Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UnaryOperatorCommandTest extends TestCase
{
    protected CommandContext $commandContext;
    
    protected function setUp(): void
    {
        $this->commandContext = new CommandContext();
    }
    
    #[DataProvider('dataRunWithValidOperands')]
    public function testRunWithValidOperands($operator, $value, $expected)
    {
        $command = new UnaryOperatorCommand(
            $operator,
            $this->mockChildCommand($value)
        );
        $this->assertEquals(
            $expected,
            $command->run($this->commandContext)
        );
    }
    
    public static function dataRunWithValidOperands()
    {
        return array(
            // `not` operator
            ['not', true,  false],
            ['not', false, true],
            ['not', 1, false],
            ['not', 0, true],

            // `-` operator
            ['-', 2, -2],
            ['-', -2.5, 2.5],
            ['-', 0, 0],
            ['-', true, -1],
        );
    }

    public function testRunMinusWithInvalidOperand()
    {
        $this->expectException(UnsupportedOperandTypeException::class);

        $command = new UnaryOperatorCommand('-', $this->mockChildCommand('foo'));
        $command->run($this->commandContext);
    }
    
    protected function mockChildCommand($returnValue)
    {
        $mock = $this->createMock(CommandInterface::class);
        $mock->method('run')->willReturn($returnValue);
        return $mock;
    }
}
