<?php

use Mormat\FormulaInterpreter\Command\NumericCommand;
use Mormat\FormulaInterpreter\Command\CommandFactory\NumericCommandFactory;

/**
 * Description of NumericCommandFactory
 *
 * @author mormat
 */
class NumericCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    /**
     *  @dataProvider getData
     */
    public function testCreate($value) {
        $factory = new NumericCommandFactory();
        $options = array('value' => $value);
        $this->assertEquals($factory->create($options), new NumericCommand($value));
    }
    
    public function getData() {
        return array(
            array('2'),
            array('4'),
        );
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingValueOption() {
        $factory = new NumericCommandFactory();
        $factory->create(array());
    }
    
}
