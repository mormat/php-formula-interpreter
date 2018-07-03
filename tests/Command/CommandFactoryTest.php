<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command;

use FormulaInterpreter\Command\CommandFactory;
use FormulaInterpreter\Command\CommandFactory\CommandFactoryException;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class CommandFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $factory = new CommandFactory();

        $command = new CommandFactoryTest_FakeCommand();
        $numericFactory = $this->createMock('\FormulaInterpreter\Command\CommandFactory');
        $numericFactory->expects($this->once())
                ->method('create')
                ->will($this->returnValue($command));
        $factory->registerFactory('numeric', $numericFactory);

        $this->assertEquals($factory->create(['type' => 'numeric']), $command);
    }

    public function testMissingTypeOption()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new CommandFactory();

        $factory->create([]);
    }

    public function testUnknownType()
    {
        $this->expectException(CommandFactoryException::class);
        $factory = new CommandFactory();

        $factory->create(['type' => 'numeric']);
    }
}

class CommandFactoryTest_FakeCommand implements \FormulaInterpreter\Command\CommandInterface
{
    public function run()
    {
    }

    public function getParameters()
    {
        return [];
    }
}
