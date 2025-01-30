<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\BooleanCommandFactory;
use Mormat\FormulaInterpreter\Command\BooleanCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanCommandFactoryTest extends TestCase
{
    #[DataProvider('getData')]
    public function testCreate($value)
    {
        $factory = new BooleanCommandFactory();
        $options = array('value' => $value);
        $this->assertEquals($factory->create($options), new BooleanCommand($value));
    }
    
    public static function getData()
    {
        return array(
            [true],
            [false],
        );
    }
    
    public function testCreateWithMissingValueOption()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new BooleanCommandFactory();
        $factory->create(array());
    }
}
