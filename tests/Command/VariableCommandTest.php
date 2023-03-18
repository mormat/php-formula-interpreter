<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\VariableCommand;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class VariableCommandTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider getData
     */
    public function testRunWhenVariablesExists($name, $variables, $result) {
        $command = new VariableCommand($name, $variables);
        
        $this->assertEquals($command->run(), $result);
    }
    
    public function getData() {
        return array(
            array('rate', array('rate' => 2), 2),
            array('price', array('price' => 32.2), 32.2),
        );
    }
    
    /**
     * @expectedException FormulaInterpreter\Exception\UnknownVariableException
     */
    public function testRunWhenVariableNotExists() {
        $command = new VariableCommand('rate', array());
        $command->run();
    }
    
    public function testRunWhenVariablesHolderImplementsArrayAccess() {
        $variables = $this->getMock('\ArrayAccess');
        $variables->expects($this->any())
            ->method('offsetExists')
            ->with($this->equalTo('rate'))
            ->will($this->returnValue(true));
        $variables->expects($this->any())
            ->method('offsetGet')
            ->with($this->equalTo('rate'))
            ->will($this->returnValue(23));
        
        $command = new VariableCommand('rate', $variables);
        
        $this->assertEquals($command->run(), 23);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getIncorrectNames
     */
    public function testInjectIncorrectName($name) {
        $command = new VariableCommand($name, array());
    }
    
    public function getIncorrectNames() {
        return array(
            array(12),
            array(False),
            array(array()),
            array(new StdClass()),
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getIncorrectVariables
     */
    public function testInjectIncorrectVariables($variables) {
        $command = new VariableCommand('rate', $variables);
    }
    
    public function getIncorrectVariables() {
        return array(
            array(12),
            array(False),
            array('string'),
            array(new StdClass()),
        );
    }
    
}

?>
