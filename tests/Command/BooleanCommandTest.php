<?php

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\BooleanCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanCommandTest extends TestCase {
    
    protected CommandContext $commandContext;
    
    public function setUp(): void
    {
        $this->commandContext = new CommandContext();
    }
    
    #[DataProvider('getData')]
    public function testRun($value, $result) {
        $command = new BooleanCommand($value);
        
        $this->assertEquals($command->run($this->commandContext), $result);
    }
    
    public static function getData() {
        return array(
            [true, true],
            [false, false],
        );
    }
    
    #[DataProvider('getIncorrectValues')]
    public function testInjectIncorrectValue($value) {
        $this->expectException(\TypeError::class);
        $command = new BooleanCommand($value);
        $command->run($this->commandContext);
    }

    public static function getIncorrectValues() {
        return array(
            [array()],
        );
    }
        
}
