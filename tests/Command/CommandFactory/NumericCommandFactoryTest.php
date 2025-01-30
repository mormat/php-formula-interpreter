<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\NumericCommandFactory;
use Mormat\FormulaInterpreter\Command\NumericCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NumericCommandFactoryTest extends TestCase
{
    #[DataProvider('getData')]
    public function testCreate($value)
    {
        $factory = new NumericCommandFactory();
        $options = array('value' => $value);
        $this->assertEquals($factory->create($options), new NumericCommand($value));
    }
    
    public static function getData()
    {
        return array(
            array('2'),
            array('4'),
        );
    }
    
    public function testCreateWithMissingValueOption()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new NumericCommandFactory();
        $factory->create(array());
    }
}
