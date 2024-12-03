<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\OperationCommand;
use Mormat\FormulaInterpreter\Exception\UnsupportedOperandTypeException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OperationCommandTest extends TestCase {
    
    protected CommandContext $commandContext;
    
    protected function setUp(): void {
        $this->commandContext = new CommandContext();
    }
    
    #[DataProvider('dataWithInvalidOperands')]
    public function testRunWithInvalidOperands($left, $operator, $right) {
        
        $this->expectException(UnsupportedOperandTypeException::class);
        
        $command = new OperationCommand(
            $this->mockChildCommand($left), 
            $operator, 
            $this->mockChildCommand($right)
        );
        
        $command->run($this->commandContext);
    }
    
    public static function dataWithInvalidOperands() {
        return array(
            // additions
            [ 'foo', '+', 'bar' ],
            [ 2,     '+', 'foo '],
            ['foo',  '+',  2    ],
            
            // substractions
            [ 'foo', '-', 'bar'],
            [ 2,     '-', 'foo'],
            ['foo',   '-', 2   ],
            
            // multiplications
            [ 'foo', '*', 'bar'],
            [ 2,    '*', 'foo' ],
            ['foo', '*', 2 ],
            
            // divisions
            [ 'foo', '/', 'bar' ],
            [ 2,     '/', 'foo' ],
            ['foo',  '/',  2    ],
          
            // 'in' operator
            [ 2, 'in', 2 ],
        );
    }

    #[DataProvider('dataRunWithValidOperands')]
    public function testRunWithValidOperands($left, $operator, $right, $expected) {
        $command = new OperationCommand(
            $this->mockChildCommand($left), 
            $operator, 
            $this->mockChildCommand($right)
        );
        $this->assertEquals(
            $expected,
            $command->run($this->commandContext)
        );
    }
    
    public static function dataRunWithValidOperands() {
        return array(
            ['1', '+', '2', 3],
            ['3', '-', '1', 2],
            ['2', '*', '3', 6],
            ['5', '/', '2', 2.5],
            
            [2,     'in', [1, 2, 3], true],
            [6,     'in', [1, 2, 3], false],
            ['foo', 'in', 'foobar',  true],
            ['baz', 'in', 'foobar',  false],
          
            array( 1, '<', 2, true),
            array( 1, '<',1, false),
            array( 3, '>',2, true),
            array( 2, '>',2, false),
            array( 2, '=',2, true),
            array( 3, '=',2, false),
            array( 1, '<=',2, true),
            array( 2, '<=',2, true),
            array( 3, '<=',2, false),
            array( 3, '>=', 2, true),
            array( 2, '>=', 2, true),
            array( 1, '>=', 2, false),
            
            array('a', '<', 'b', true),
            array('a', '<', 'a', false),
            array('b', '>','a', true),
            array( 'b','>', 'b', false),
            array( 'a','=', 'a', true),
            array( 'a','=', 'b', false),
            array( 'a','<=', 'b', true),
            array( 'b','<=', 'b', true),
            array( 'c','<=', 'b', false),
            array( 'c','>=', 'b', true),
            array( 'b','>=', 'b', true),
            array( 'a','>=', 'b', false),
        );
    }
    
    protected function mockChildCommand($returnValue) {
        $mock = $this->createMock(CommandInterface::class);
        $mock->method('run')->willReturn($returnValue);
        return $mock;
    }
    
}
