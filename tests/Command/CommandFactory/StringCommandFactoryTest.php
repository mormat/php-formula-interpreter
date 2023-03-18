<?php

use Mormat\FormulaInterpreter\Command\StringCommand;
use Mormat\FormulaInterpreter\Command\CommandFactory\StringCommandFactory;

/**
 * Tests the creation of a command to execute a string expression
 *
 * @author mormat
 */
class StringCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    /**
     *  @dataProvider getData
     */
    public function testCreate($value) {
        $factory = new StringCommandFactory();
        $options = array('value' => $value);
        $this->assertEquals($factory->create($options), new StringCommand($value));
    }
    
    public function getData() {
        return array(
            array('foo'),
            array('bar'),
        );
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingValueOption() {
        $factory = new StringCommandFactory();
        $factory->create(array());
    }
    
}
