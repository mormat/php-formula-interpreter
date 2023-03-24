<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\VariableCommand;

/**
 * Test the command to execute of a variable
 *
 * @author mormat
 */
class VariableCommandTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider getData
     */
    public function testRunWhenVariablesExists($name, $variables, $result) {
        $command = new VariableCommand($name);
        
        $context = new CommandContext($variables);
        
        $this->assertEquals($command->run($context), $result);
    }
    
    public function getData() {
        return array(
            array('rate', array('rate' => 2), 2),
            array('price', array('price' => 32.2), 32.2),
        );
    }
    
    /**
     * @expectedException \Mormat\FormulaInterpreter\Exception\UnknownVariableException
     */
    public function testRunWhenVariableNotExists() {
        $context = new CommandContext([]);
        
        $command = new VariableCommand('rate', array());
        $command->run($context);
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
    
}
