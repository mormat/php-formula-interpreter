<?php

use Mormat\FormulaInterpreter\Command\ArrayCommand;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\ArrayCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;

/**
 * Tests the creation of ArrayCommand
 *
 * @author mormat
 */
class ArrayCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
        /* 
        $this->itemCommandMock = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        */
        $this->itemCommandFactoryMock = $this->getMockBuilder(
            CommandFactoryInterface::class
        )->getMock();
        $this->itemCommandFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(function($value) {
                return 'mocked ' . $value;
            }));
        
    }
    
    public function testCreate() {
        
        $factory = new ArrayCommandFactory(
            $this->itemCommandFactoryMock
        );
        
        $options = array(
            'value' => [1, 2],
        );
        
        $this->assertEquals(
            $factory->create($options),
            new ArrayCommand(['mocked 1', 'mocked 2'])
        );
        
    }
    
}
