<?php

use \Mormat\FormulaInterpreter\Command\NumericCommand;
use \Mormat\FormulaInterpreter\Command\StringCommand;
use \Mormat\FormulaInterpreter\Command\VariableCommand;
use \Mormat\FormulaInterpreter\Visitor\ValidationVisitor;

class ValidationVisitorTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @dataProvider getValidCommandsData
     */
    public function testAcceptWithValidCommands($command)
    {
        $validation = new ValidationVisitor(
            ['foo' => 2]
        );
        $validation->accept($command);
        $this->assertEquals([], $validation->getErrors());        
    }
    
    public function getValidCommandsData()
    {
        return array(
            [new NumericCommand(10)],
            [new StringCommand('10')],
            [new VariableCommand('foo')]
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
        );
    }
    
}

