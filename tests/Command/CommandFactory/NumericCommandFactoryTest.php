<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

use FormulaInterpreter\Command\NumericCommand;
use FormulaInterpreter\Command\CommandFactory\NumericCommandFactory;

/**
 * Description of NumericCommandFactory
 *
 * @author mathieu
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
     * @expectedException FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingValueOption() {
        $factory = new NumericCommandFactory();
        $factory->create(array());
    }
    
}

?>
