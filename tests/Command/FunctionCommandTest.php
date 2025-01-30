<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\FunctionCommand;
use Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException;
use Mormat\FormulaInterpreter\Exception\UnknownFunctionException;
use Mormat\FormulaInterpreter\Functions\FunctionInterface;

use PHPUnit\Framework\TestCase;

class FunctionCommandTest extends TestCase
{
    protected CommandContext $commandContext;
    
    public function setUp(): void
    {
        $this->commandContext = new CommandContext([], $this->getFunctions());
    }
    
    protected function getFunctions()
    {
        $functions = array();
        foreach (['pi', 'increment', 'add', 'invalid_params'] as $name) {
            $functions[$name] = $this->getMockBuilder(FunctionInterface::class)->getMock();
            $functions[$name]->method('getName')->willReturn($name);
        }
        
        $functions['pi']->method('supports')->willReturn(true);
        $functions['pi']->method('execute')->willReturn(3.14);
        
        $functions['increment']->method('supports')->willReturn(true);
        $functions['increment']->method('execute')->willReturnCallback(
            fn($params) => $params[0] + 1
        );
        
        $functions['add']->method('supports')->willReturn(true);
        $functions['add']->method('execute')->willReturnCallback(
            fn($params) => $params[0] + $params[1]
        );
        
        $functions['invalid_params']->method('supports')->willReturn(false);
        
        return $functions;
    }
    
    public function testRunWithoutArguments()
    {
        $command = new FunctionCommand('pi');
        $this->assertEquals($command->run($this->commandContext), 3.14);
    }
    
    public function testRunWithOneArgument()
    {
        $command = new FunctionCommand('increment', array(
            $this->mockArgumentCommand(4)
        ));
        
        $this->assertEquals($command->run($this->commandContext), 5);
    }
    
    public function testRunWithTwoArgument()
    {
        $command = new FunctionCommand('add', array(
            $this->mockArgumentCommand(2),
            $this->mockArgumentCommand(3)
        ));
        
        $this->assertEquals($command->run($this->commandContext), 5);
    }
    
    public function testRunWithInvalidParameters()
    {
        $this->expectException(InvalidParametersFunctionException::class);
        $this->expectExceptionMessage("Invalid parameters provided to function 'invalid_params'");
        $command = new FunctionCommand('invalid_params');
        $command->run($this->commandContext);
    }
    
    public function testRunWhenFunctionNotExists()
    {
        $this->expectException(UnknownFunctionException::class);
        $this->expectExceptionMessage('Unknown function "cos"');
        $command = new FunctionCommand('cos', array());
        $command->run($this->commandContext);
    }
    
    public function tesArgumentCommandsMustImplementCommandInterface()
    {
        $this->expectException(\TypeError::class);
        new FunctionCommand('cos', array('some string'));
    }
    
    protected function mockArgumentCommand($returnedValue)
    {
        $argumentCommand = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $argumentCommand->expects($this->once())
            ->method('run')
            ->will($this->returnValue($returnedValue));
        return $argumentCommand;
    }
}
