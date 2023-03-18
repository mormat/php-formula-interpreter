<?php

use Mormat\FormulaInterpreter\Command\VariableCommand;
use Mormat\FormulaInterpreter\Command\CommandFactory\VariableCommandFactory;

/**
 * Description of VariableCommandFactory
 *
 * @author mormat
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
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingNameOption() {
        $factory = new VariableCommandFactory(array());
        $factory->create(array());
    }
    
}
