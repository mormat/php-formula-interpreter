<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\FunctionCommand;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class FunctionCommandTest extends PHPUnit_Framework_TestCase {
    
    public function testRunWithoutArguments() {
        $callable = function() {
          return 2;
        };
        
        $command = new FunctionCommand($callable);
        $this->assertEquals($command->run(), 2);
        
    }
    
    public function testRunWithOneArgument() {
        $callable = function($arg) {
          return $arg;
        };
        
        $argumentCommand = $this->getMock('\FormulaInterpreter\Command\CommandInterface');
        $argumentCommand->expects($this->once())
                ->method('run')
                ->will($this->returnValue(4));
        $command = new FunctionCommand($callable, array($argumentCommand));
        
        $this->assertEquals($command->run(), 4);
  
    }
    
    public function testRunWithTwoArgument() {
        $callable = function($arg1, $arg2) {
          return $arg1 + $arg2;
        };
        
        $argumentCommands = array();
        foreach (array(2, 3) as $value) {
            $argumentCommand = $this->getMock('\FormulaInterpreter\Command\CommandInterface');
            $argumentCommand->expects($this->any())
                    ->method('run')
                    ->will($this->returnValue($value));
            $argumentCommands[] = $argumentCommand;
        }
        
        $command = new FunctionCommand($callable, $argumentCommands);
        
        $this->assertEquals($command->run(), 5);
  
    }
    
    /**
     * @expectedException FormulaInterpreter\Exception\NotEnoughArgumentsException
     */
    public function testRunWithMissingArguments() {
        $callable = function($arg1) {};
        
        $command = new FunctionCommand($callable);  
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructWhenArgumentCommandDontImplementInterfaceCommand() {
        $callable = function($arg1) {};
        
        $command = new FunctionCommand($callable, array('whatever'));  
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructWhenCallableParameterIsNotCallable() {
        $callable = 23;
        
        $command = new FunctionCommand($callable);  
    }
    


    
}

?>
