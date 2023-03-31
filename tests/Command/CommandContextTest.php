<?php

use Mormat\FormulaInterpreter\Command\CommandContext;

class CommandContextTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Should not throw exception if $variables is not an array
     * 
     * @dataProvider getIncorrectVariables
     */
    public function testInjectIncorrectVariables($variables) {
        $context = new CommandContext($variables);
        $this->assertFalse($context->hasVariable('foo'));
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
