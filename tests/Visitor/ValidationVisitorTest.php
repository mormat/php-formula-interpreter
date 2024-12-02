<?php

use \Mormat\FormulaInterpreter\Command\NumericCommand;
use \Mormat\FormulaInterpreter\Command\StringCommand;
use \Mormat\FormulaInterpreter\Command\VariableCommand;
use \Mormat\FormulaInterpreter\Command\FunctionCommand;
use \Mormat\FormulaInterpreter\Functions\FunctionInterface;
use \Mormat\FormulaInterpreter\Visitor\ValidationVisitor;


class ValidationVisitorTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider getValidCommandsData
     */
    public function testAcceptWithValidCommands($command)
    {
        $validation = new ValidationVisitor(
            ['foo' => 2],
            ['cos' => $this->getMockBuilder(FunctionInterface::class)->getMock()]
        );
        $validation->accept($command);
        $this->assertEquals([], $validation->getErrors());        
    }
    
    public function getValidCommandsData()
    {
        return array(
            [new NumericCommand(10)],
            [new StringCommand('10')],
            [new VariableCommand('foo')],
            [new FunctionCommand('cos')],
        );
    }
    
    /**
     * @dataProvider getInvalidCommandsData
     */
    public function testAcceptWithInvalidCommands($command, $errors)
    {
        $validation = new ValidationVisitor();
        $validation->accept($command);
        $this->assertEquals($errors, $validation->getErrors());        
    }
    
    public function getInvalidCommandsData()
    {
        return array(
            [new VariableCommand('foo'), array(
                ['type' => 'unknown_variable', 'value' => 'foo']
            )],
            [new FunctionCommand('cos'), array(
                ['type' => 'unknown_function', 'value' => 'cos']
            )],
        );
    }
    
}

