<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\VariableCommand;
use FormulaInterpreter\Command\CommandFactory\VariableCommandFactory;

/**
 * Description of VariableCommandFactory
 *
 * @author mathieu
 */
class VariableCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    /**
     *  @dataProvider getData
     */
    public function testCreate($name, $variables) {
        $factory = new VariableCommandFactory($variables);
        $options = array('name' => $name);
        $this->assertEquals($factory->create($options), new VariableCommand($name, $variables));
    }
    
    public function getData() {
        return array(
            array('rate', array('rate' => 4)),
            array('price', array('price' => 4)),
            array('price', array('price' => 40)),
        );
    }
    
    /**
     * @expectedException FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingNameOption() {
        $factory = new VariableCommandFactory(array());
        $factory->create(array());
    }
    
}

?>
