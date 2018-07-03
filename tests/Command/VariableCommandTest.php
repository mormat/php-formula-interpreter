<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command;

use FormulaInterpreter\Command\VariableCommand;
use FormulaInterpreter\Exception\UnknownVariableException;
use PHPUnit\Framework\TestCase;

/**
 * Description of ParserTest
 *
 * @author mathieu
 */
class VariableCommandTest extends TestCase
{

    /**
     * @dataProvider getData
     */
    public function testRunWhenVariablesExists($name, $variables, $result)
    {
        $command = new VariableCommand($name, $variables);

        $this->assertEquals($command->run(), $result);
    }

    public function getData()
    {
        return [
            ['rate', ['rate' => 2], 2],
            ['price', ['price' => 32.2], 32.2],
        ];
    }

    public function testRunWhenVariableNotExists()
    {
        $this->expectException(UnknownVariableException::class);
        $command = new VariableCommand('rate', []);
        $command->run();
    }

    public function testRunWhenVariablesHolderImplementsArrayAccess()
    {
        $variables = $this->createMock(\ArrayAccess::class);
        $variables->expects($this->any())
            ->method('offsetExists')
            ->with($this->equalTo('rate'))
            ->will($this->returnValue(true));
        $variables->expects($this->any())
            ->method('offsetGet')
            ->with($this->equalTo('rate'))
            ->will($this->returnValue(23));

        $command = new VariableCommand('rate', $variables);

        $this->assertEquals($command->run(), 23);
    }

    /**
     * @dataProvider getIncorrectNames
     */
    public function testInjectIncorrectName($name)
    {
        $this->expectException(\InvalidArgumentException::class);
        $command = new VariableCommand($name, []);
    }

    public function getIncorrectNames()
    {
        return [
            [12],
            [false],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @dataProvider getIncorrectVariables
     */
    public function testInjectIncorrectVariables($variables)
    {
        $this->expectException(\InvalidArgumentException::class);
        $command = new VariableCommand('rate', $variables);
    }

    public function getIncorrectVariables()
    {
        return [
            [12],
            [false],
            ['string'],
            [new \stdClass()],
        ];
    }
}
