<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use FormulaInterpreter\Command\VariableCommand;
use FormulaInterpreter\Command\CommandFactory\VariableCommandFactory;

/**
 * Description of VariableCommandFactory
 *
 * @author mathieu
 */
class VariableCommandFactoryTest extends \PHPUnit\Framework\TestCase
{
    
    /**
     *  @dataProvider getData
     */
    public function testCreate($name, $variables)
    {
        $factory = new VariableCommandFactory($variables);
        $options = ['name' => $name];
        $this->assertEquals($factory->create($options), new VariableCommand($name, $variables));
    }
    
    public function getData()
    {
        return [
            ['rate', ['rate' => 4]],
            ['price', ['price' => 4]],
            ['price', ['price' => 40]],
        ];
    }

    public function testCreateWithMissingNameOption()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new VariableCommandFactory([]);
        $factory->create([]);
    }
}
