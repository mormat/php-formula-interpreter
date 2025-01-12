<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\UnaryOperatorCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\UnaryOperatorCommand;

use PHPUnit\Framework\TestCase;

class UnaryOperatorCommandFactoryTest extends TestCase {
    
    public function testCreate() {
        
        $operands = [
            'some_value'  => $this->createMock(CommandInterface::class),
        ];
        
        $childCommandFactory = $this->createMock(CommandFactoryInterface::class);
        $childCommandFactory->method('create')->willReturnCallback(
            fn($value) => $operands[$value] ?? null
        );
        
        $factory = new UnaryOperatorCommandFactory($childCommandFactory);
        
        $actual = $factory->create([
            'value'     => 'some_value',
            'operator' => '+',
        ]);
        
        $this->assertEquals(
            new UnaryOperatorCommand(
                 '+', 
                $operands['some_value']
            ),
            $actual
        );
        
    }
    
}