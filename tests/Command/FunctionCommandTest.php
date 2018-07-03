<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command;

use FormulaInterpreter\Command\FunctionCommand;
use FormulaInterpreter\Exception\NotEnoughArgumentsException;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class FunctionCommandTest extends \PHPUnit\Framework\TestCase
{
    public function testRunWithoutArguments()
    {
        $callable = function () {
            return 2;
        };

        $command = new FunctionCommand($callable);
        $this->assertEquals($command->run(), 2);
    }

    public function testRunWithOneArgument()
    {
        $callable = function ($arg) {
            return $arg;
        };

        $argumentCommand = $this->createMock('\FormulaInterpreter\Command\CommandInterface');
        $argumentCommand->expects($this->once())
                ->method('run')
                ->will($this->returnValue(4));
        $command = new FunctionCommand($callable, [$argumentCommand]);

        $this->assertEquals($command->run(), 4);
    }

    public function testRunWithTwoArgument()
    {
        $callable = function ($arg1, $arg2) {
            return $arg1 + $arg2;
        };

        $argumentCommands = [];
        foreach ([2, 3] as $value) {
            $argumentCommand = $this->createMock('\FormulaInterpreter\Command\CommandInterface');
            $argumentCommand->expects($this->any())
                    ->method('run')
                    ->will($this->returnValue($value));
            $argumentCommands[] = $argumentCommand;
        }

        $command = new FunctionCommand($callable, $argumentCommands);

        $this->assertEquals($command->run(), 5);
    }

    public function testRunWithMissingArguments()
    {
        $this->expectException(NotEnoughArgumentsException::class);
        $callable = function ($arg1) {
        };

        $command = new FunctionCommand($callable);
    }

    public function testConstructWhenArgumentCommandDontImplementInterfaceCommand()
    {
        $this->expectException(\InvalidArgumentException::class);
        $callable = function ($arg1) {
        };

        $command = new FunctionCommand($callable, ['whatever']);
    }

    public function testConstructWhenCallableParameterIsNotCallable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $callable = 23;

        $command = new FunctionCommand($callable);
    }
}
