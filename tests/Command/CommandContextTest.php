<?php

use Mormat\FormulaInterpreter\Command\CommandContext;

class CommandContextTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getIncorrectVariables
     */
    public function testInjectIncorrectVariables($variables) {
        new CommandContext($variables);
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
