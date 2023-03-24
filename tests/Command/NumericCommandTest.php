<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\NumericCommand;

/**
 * Description of ParserTest
 *
 * @author mormat
 */
class NumericCommandTest extends PHPUnit_Framework_TestCase {
    
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
        $command = new NumericCommand($value);
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public function getData() {
        return array(
            array(2, 2),
            array(2.2, 2.2),
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getIncorrectValues
     */
    public function testInjectIncorrectValue($value) {
        $command = new NumericCommand($value);
        $command->run($this->commandContext);
    }

    public function getIncorrectValues() {
        return array(
            array('string'),
            array(false),
            array(array()),
        );
    }
    
}
