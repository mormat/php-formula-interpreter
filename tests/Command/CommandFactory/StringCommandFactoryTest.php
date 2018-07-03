<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use FormulaInterpreter\Command\CommandFactory\StringCommandFactory;
use FormulaInterpreter\Command\NumericCommand;
use FormulaInterpreter\Command\CommandFactory\NumericCommandFactory;
use FormulaInterpreter\Command\StringCommand;
use PHPUnit\Framework\TestCase;

/**
 * Description of NumericCommandFactory
 *
 * @author Petra Barus <petra.barus@gmail.com>
 */
class StringCommandFactoryTest extends TestCase
{
    
    /**
     *  @dataProvider getData
     */
    public function testCreate($value)
    {
        $factory = new StringCommandFactory();
        $options = ['value' => $value];
        $this->assertEquals($factory->create($options), new StringCommand($value));
    }
    
    public function getData()
    {
        return [
            ['2'],
            ['4'],
        ];
    }
    
    public function testCreateWithMissingValueOption()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new StringCommandFactory();
        $factory->create([]);
    }
}
