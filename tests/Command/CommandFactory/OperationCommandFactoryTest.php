<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\OperationCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\OperationCommand;

use PHPUnit\Framework\TestCase;

class OperationCommandFactoryTest extends TestCase
{
    public function testCreate()
    {
        $operands = [
            'left_value'  => $this->createMock(CommandInterface::class),
            'right_value' => $this->createMock(CommandInterface::class)
        ];
        
        $childCommandFactory = $this->createMock(CommandFactoryInterface::class);
        $childCommandFactory->method('create')->willReturnCallback(
            fn($value) => $operands[$value] ?? null
        );
        
        $factory = new OperationCommandFactory($childCommandFactory);
        
        $actual = $factory->create([
            'left'     => 'left_value',
            'operator' => '+',
            'right'    => 'right_value',
        ]);
        
        $this->assertEquals(
            new OperationCommand(
                $operands['left_value'],
                '+',
                $operands['right_value']
            ),
            $actual
        );
    }
}
