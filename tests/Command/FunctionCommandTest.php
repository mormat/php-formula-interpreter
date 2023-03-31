<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\FunctionCommand;
use Mormat\FormulaInterpreter\Functions\FunctionInterface;

/**
 * Test execution of function
 *
 * @author mormat
 */
class FunctionCommandTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ContextCommand
     */
    protected $commandContext;
    
    public function setUp()
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
        $functions['increment']->method('execute')->willReturnCallback(function($params) {
            return $params[0] + 1;
        });
        
        $functions['add']->method('supports')->willReturn(true);
        $functions['add']->method('execute')->willReturnCallback(function($params) {
            return $params[0] + $params[1];
        });
        
        $functions['invalid_params']->method('supports')->willReturn(false);
        
        return $functions;
                
    }
    
    public function testRunWithoutArguments() {
        
        $command = new FunctionCommand('pi');
        $this->assertEquals($command->run($this->commandContext), 3.14);
        
    }
    
    public function testRunWithOneArgument() {

        $command = new FunctionCommand('increment', array(
            $this->mockArgumentCommand(4)
        ));
        
        $this->assertEquals($command->run($this->commandContext), 5);
  
    }
    
    public function testRunWithTwoArgument() {
        
        $command = new FunctionCommand('add', array(
            $this->mockArgumentCommand(2),
            $this->mockArgumentCommand(3)
        ));
        
        $this->assertEquals($command->run($this->commandContext), 5);
        
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Exception\InvalidParametersFunctionException
     * @expectedExceptionMessage Invalid parameters provided to function 'invalid_params'
     */
    public function testRunWithInvalidParameters() {
        
        $command = new FunctionCommand('invalid_params');  
        $command->run($this->commandContext);
    }
    
    /**
     * @expectedException \Mormat\FormulaInterpreter\Exception\UnknownFunctionException
     * @expectedExceptionMessage Unknown function "cos"
     */
    public function testRunWhenFunctionNotExists() {
        
        $command = new FunctionCommand('cos', array());
        $command->run($this->commandContext);
    }
    
    /**
     * @expectedException \TypeError
     */
    public function tesArgumentCommandsMustImplementCommandInterface() {
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
