<?php

use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\StringCommandFactory;
use Mormat\FormulaInterpreter\Command\StringCommand;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the creation of a command to execute a string expression
 *
 * @author mormat
 */
class StringCommandFactoryTest extends TestCase {
    
    #[DataProvider('getData')]
    public function testCreate($value) {
        $factory = new StringCommandFactory();
        $options = array('value' => $value);
        $this->assertEquals($factory->create($options), new StringCommand($value));
    }
    
    public static function getData() {
        return array(
            array('foo'),
            array('bar'),
        );
    }
    
    public function testCreateWithMissingValueOption() {
        $this->expectException(CommandFactoryException::class);
        $factory = new StringCommandFactory();
        $factory->create(array());
    }
    
}
