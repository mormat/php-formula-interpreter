<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\StringCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the execution of a string expression
 */
class StringCommandTest extends TestCase
{
    protected CommandContext $commandContext;
    
    public function setUp(): void
    {
        $this->commandContext = new CommandContext();
    }
    
    #[DataProvider('getData')]
    public function testRun($value, $result)
    {
        $command = new StringCommand($value);
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public static function getData()
    {
        return array(
            array('foo', 'foo'),
            array('bar', 'bar'),
        );
    }
    
    #[DataProvider('getIncorrectValues')]
    public function testInjectIncorrectValue($value)
    {
        $this->expectException(\TypeError::class);
        $command = new StringCommand($value);
        $command->run($this->commandContext);
    }

    public static function getIncorrectValues()
    {
        return array(
            array(4),
            array(false),
            array(array()),
        );
    }
}
