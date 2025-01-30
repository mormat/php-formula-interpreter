<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\FunctionCommandFactory;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\FunctionCommand;

use PHPUnit\Framework\TestCase;

/**
 * Description of NumericCommandFactory
 */
class FunctionCommandFactoryTest extends TestCase
{
    protected CommandFactoryInterface $argumentCommandFactory;
    protected FunctionCommandFactory $factory;
    
    public function setUp(): void
    {
        $this->argumentCommandFactory = $this->getMockBuilder(
            CommandFactoryInterface::class
        )->getMock();
        $this->factory = new FunctionCommandFactory($this->argumentCommandFactory);
    }
    
    public function testCreateShouldReturnFunctionCommand()
    {
        $options = array('name' => 'pi');
        $object = $this->factory->create($options);
        $this->assertTrue($object instanceof FunctionCommand, 'An instance of FunctionCommand should be returned');
    }
    
    public function testCreateWithNoArguments()
    {
        $options = array('name' => 'pi');
        $object = $this->factory->create($options);
        
        $this->assertEquals(
            self::getObjectProperty($object, 'function'),
            'pi',
        );
        $this->assertEquals(
            self::getObjectProperty($object, 'argumentCommands'),
            array(),
        );
    }
    
    public function testCreateWithArguments()
    {
        $argumentCommand = $this->getMockBuilder(
            CommandInterface::class
        )->getMock();
        $this->argumentCommandFactory->expects($this->once())
                ->method('create')
                ->with($this->equalTo(array('type' => 'fake')))
                ->will($this->returnValue($argumentCommand));
        
        $options = array(
            'name' => 'pi',
            'arguments' => array(array('type' => 'fake'))
        );
        $object = $this->factory->create($options);
        $this->assertEquals(
            self::getObjectProperty($object, 'function'),
            'pi',
        );
        $this->assertEquals(
            self::getObjectProperty($object, 'argumentCommands'),
            array($argumentCommand),
        );
    }
    
    public function testCreateWithMissingNameOption()
    {
        $this->expectException(CommandFactoryException::class);
        $this->factory->create(array());
    }
    
    protected static function getObjectProperty($object, $property)
    {
        $reflectedClass = new \ReflectionClass($object);
        $reflection = $reflectedClass->getProperty($property);
        $reflection->setAccessible(true);
        return $reflection->getValue($object);
    }
}
