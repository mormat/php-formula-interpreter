<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\FormulaInterpreter\Command\CommandFactory;

use FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use FormulaInterpreter\Command\OperationCommand;
use FormulaInterpreter\Command\CommandInterface;
use FormulaInterpreter\Command\CommandFactory\OperationCommandFactory;
use PHPUnit\Framework\TestCase;

/**
 * Description of OperationCommandFactory
 *
 * @author mathieu
 */
class OperationCommandFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->factory = new OperationCommandFactory($this->createCommandFactoryMock());
    }

    /**
     *
     */
    public function testCreateWithOneOperand()
    {
        $options = [
            'firstOperand' => [2],
        ];
        $this->assertEquals($this->factory->create($options), new OperationCommand(new OperationCommandFactoryTest_FakeCommand([2])));
    }

    /**
     *
     */
    public function testCreateWithEmptyOthersOperands()
    {
        $options = [
            'firstOperand' => [2],
            'otherOperands' => [
                [],
            ],
        ];
        $this->assertEquals($this->factory->create($options), new OperationCommand(new OperationCommandFactoryTest_FakeCommand([2])));
    }


    /**
     *
     */

    public function testCreateWithTwoOperands()
    {
        $options = [
            'firstOperand' => [2],
            'otherOperands' => [
                ['operator' => 'add', 'value' =>  ['3']]
            ]
        ];

        $expected = new OperationCommand(new OperationCommandFactoryTest_FakeCommand([2]));
        $expected->addOperand('add', new OperationCommandFactoryTest_FakeCommand([3]));

        $this->assertEquals($this->factory->create($options), $expected);
    }

    public function testCreateWithMissingFirstOperandOption()
    {
        $this->expectException(CommandFactoryException::class);
        $this->factory->create([]);
    }

    protected function createCommandFactoryMock()
    {
        $operandFactory = $this->createMock(CommandFactoryInterface::class);
        $operandFactory->expects($this->any())
                ->method('create')
                ->will($this->returnCallback([OperationCommandFactoryTest::class, 'createFakeCommand']));
        return $operandFactory;
    }

    public static function createFakeCommand($options)
    {
        return new OperationCommandFactoryTest_FakeCommand($options);
    }
}

class OperationCommandFactoryTest_FakeCommand implements CommandInterface
{
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function run()
    {
    }

    public function getParameters()
    {
        return [];
    }
}
