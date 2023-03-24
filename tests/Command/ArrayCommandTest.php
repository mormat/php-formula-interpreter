<?php

use Mormat\FormulaInterpreter\Command\ArrayCommand;
use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;

/**
 * Tests the execution of a array expression
 *
 * @author mormat
 */
class ArrayCommandTest extends PHPUnit_Framework_TestCase {
    
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
    public function testRun($items, $result) {
        $mocker = [$this, 'createMockCommandReturningValue'];
        
        $command = new ArrayCommand(
            array_map($mocker, $items)
        );
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public function getData() {
        return array(
            array([1, 2], ['mocking 1', 'mocking 2']),
        );
    }
    
    public function createMockCommandReturningValue($value) {
        $command = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $command->expects($this->any())
            ->method('run')
            ->will($this->returnValue('mocking '.$value));
        return $command;
    }
    
}
