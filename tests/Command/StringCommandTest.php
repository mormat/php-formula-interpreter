<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\StringCommand;

/**
 * Tests the execute of a string expression
 *
 * @author mormat
 */
class StringCommandTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var ContextCommand
     */
    protected $commandContext;
    
    public function setUp()
    {
        $this->commandContext = new CommandContext();
    }
    
    /**
     * @dataProvider getData
     */
    public function testRun($value, $result) {
        $command = new StringCommand($value);
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public function getData() {
        return array(
            array('foo', 'foo'),
            array('bar', 'bar'),
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getIncorrectValues
     */
    public function testInjectIncorrectValue($value) {
        $command = new StringCommand($value);
        $command->run($this->commandContext);
    }

    public function getIncorrectValues() {
        return array(
            array(4),
            array(false),
            array(array()),
        );
    }
    
}
