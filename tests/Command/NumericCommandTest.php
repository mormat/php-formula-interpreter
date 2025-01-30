<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\NumericCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumericCommandTest extends TestCase
{
    protected CommandContext $commandContext;
    
    public function setUp(): void
    {
        $this->commandContext = new CommandContext();
    }
    
    #[DataProvider('getData')]
    public function testRun($value, $result)
    {
        $command = new NumericCommand($value);
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public static function getData()
    {
        return array(
            array(2, 2),
            array(2.2, 2.2),
        );
    }
    
    #[DataProvider('getIncorrectValues')]
    public function testInjectIncorrectValue($value)
    {
        $this->expectException(\TypeError::class);
        $command = new NumericCommand($value);
        $command->run($this->commandContext);
    }

    public static function getIncorrectValues()
    {
        return array(
            array('string'),
            array(false),
            array(array()),
        );
    }
}
