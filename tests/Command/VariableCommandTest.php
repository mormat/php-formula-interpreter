<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandContext;
use Mormat\FormulaInterpreter\Command\VariableCommand;
use Mormat\FormulaInterpreter\Exception\UnknownVariableException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test the command to execute of a variable
 */
class VariableCommandTest extends TestCase
{
    #[DataProvider('getData')]
    public function testRunWhenVariablesExists($name, $variables, $result)
    {
        $command = new VariableCommand($name);
        
        $context = new CommandContext($variables);
        
        $this->assertEquals($command->run($context), $result);
    }
    
    public static function getData()
    {
        return array(
            array('rate', array('rate' => 2), 2),
            array('price', array('price' => 32.2), 32.2),
        );
    }
    
    public function testRunWhenVariableNotExists()
    {
        $this->expectException(UnknownVariableException::class);
        $context = new CommandContext([]);
        
        $command = new VariableCommand('rate', array());
        $command->run($context);
    }
    
    #[DataProvider('getIncorrectNames')]
    public function testInjectIncorrectName($name)
    {
        $this->expectException(\TypeError::class);
        new VariableCommand($name, array());
    }
    
    public static function getIncorrectNames()
    {
        return array(
            array(12),
            array(false),
            array(array()),
            array(new \StdClass()),
        );
    }
}
