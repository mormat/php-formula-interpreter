<?php

use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\FunctionCommand;
use Mormat\FormulaInterpreter\Functions\FunctionInterface;

/**
 * Description of ParserTest
 *
 * @author mormat
 */
class FunctionCommandTest extends PHPUnit_Framework_TestCase {

    protected function createFunctionMock(callable $execute = null, callable $supports = null)
    {        
        $mock = $this->getMockBuilder(
            FunctionInterface::class
        )->getMock();
        $mock->method('getName')->willReturn('mock');
        
        $defaults = array(
            'execute'  => [$this, 'noop'],
            'supports' => function() { return true; }
        );
        $methods = array_filter(array(
            'supports' => $supports, 
            'execute'  => $execute
        )) + $defaults;
        foreach ($methods as $name => $callable) {
            $mock->method($name)
                 ->willReturnCallback(function($params) use ($callable) {
                    return call_user_func_array($callable, $params);
                });
        }
        
        return $mock;
    }
    
    public function testRunWithoutArguments() {

        $function = $this->createFunctionMock(function() {
            return 2;  
        });
           
        $command = new FunctionCommand($function);
        $this->assertEquals($command->run(), 2);
        
    }
    
    public function testRunWithOneArgument() {
        $function = $this->createFunctionMock(function($arg) {
            return $arg + 1;
        });
        
        $argumentCommand = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $argumentCommand->expects($this->once())
            ->method('run')
            ->will($this->returnValue(4));
        $command = new FunctionCommand($function, array($argumentCommand));
        
        $this->assertEquals($command->run(), 5);
  
    }
    
    public function testRunWithTwoArgument() {
        $function = $this->createFunctionMock(function($arg1, $arg2) {
          return $arg1 + $arg2;
        });
        
        $argumentCommands = array();
        foreach (array(2, 3) as $value) {
            $argumentCommand = $this->getMockBuilder(
                CommandInterface::class
            )->getMock();
            $argumentCommand->expects($this->any())
                    ->method('run')
                    ->will($this->returnValue($value));
            $argumentCommands[] = $argumentCommand;
        }
        
        $command = new FunctionCommand($function, $argumentCommands);
        
        $this->assertEquals($command->run(), 5);
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException
     * @expectedExceptionMessage Invalid parameters provided to function 'mock'
     */
    public function testRunWithInvalidParameters() {
        $function = $this->createFunctionMock(null, function() {
            return false;
        });
        
        $command = new FunctionCommand($function);  
        $command->run();
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructWhenArgumentCommandsDontImplementInterfaceCommand() {
        $function = $this->createFunctionMock();
        
        new FunctionCommand($function, array('whatever'));  
    }
    
    protected function noop() {}
    
}
