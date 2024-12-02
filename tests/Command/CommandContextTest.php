<?php

use Mormat\FormulaInterpreter\Command\CommandContext;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommandContextTest extends TestCase {
    
    /**
     * Should not throw exception if $variables is not an array
     */
    #[DataProvider('getIncorrectVariables')]
    public function testInjectIncorrectVariables($variables) {
        $context = new CommandContext($variables);
        $this->assertFalse($context->hasVariable('foo'));
    }
    
    public static function getIncorrectVariables() {
        return array(
            array(12),
            array(False),
            array('string'),
            array(new StdClass()),
        );
    }
    
}
