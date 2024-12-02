<?php

use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\VariableCommandFactory;
use Mormat\FormulaInterpreter\Command\VariableCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VariableCommandFactoryTest extends TestCase {
    
    #[DataProvider('getData')]
    public function testCreate($name, $variables) {
        $factory = new VariableCommandFactory($variables);
        $options = array('name' => $name);
        $this->assertEquals($factory->create($options), new VariableCommand($name, $variables));
    }
    
    public static function getData() {
        return array(
            array('rate', array('rate' => 4)),
            array('price', array('price' => 4)),
            array('price', array('price' => 40)),
        );
    }
    
    public function testCreateWithMissingNameOption() {
        $this->expectException(CommandFactoryException::class);
        $factory = new VariableCommandFactory(array());
        $factory->create(array());
    }
    
}
