<?php

use Mormat\FormulaInterpreter\Command\FunctionCommand;
use Mormat\FormulaInterpreter\Command\CommandInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryInterface;
use Mormat\FormulaInterpreter\Command\CommandFactory\FunctionCommandFactory;
use Mormat\FormulaInterpreter\Functions\FunctionInterface;

/**
 * Description of NumericCommandFactory
 *
 * @author mormat
 */
class FunctionCommandFactoryTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
        $this->argumentCommandFactory = $this->getMockBuilder(
            CommandFactoryInterface::class
        )->getMock();
        $this->factory = new FunctionCommandFactory($this->argumentCommandFactory);
        
        $this->piFunction = $this->getMockBuilder(
            FunctionInterface::class
        )->getMock();
        $this->piFunction->method('getName')->willReturn('pi');
        $this->piFunction->method('supports')->willReturn(true);
        $this->piFunction->method('execute')->willReturn(3.14);
        $this->factory->registerFunction($this->piFunction);
    }
    
    public function testCreateShouldReturnFunctionCommand() {
        $options = array('name' => 'pi');
        $object = $this->factory->create($options);
        $this->assertTrue($object instanceof FunctionCommand, 'An instance of FunctionCommand should be returned');
    }
    
    public function testCreateWithNoArguments() {       
        $options = array('name' => 'pi');
        $object = $this->factory->create($options);
        $this->assertObjectPropertyEquals($object, 'function', $this->piFunction);   
        $this->assertObjectPropertyEquals($object, 'argumentCommands', array());
    }
    
    public function testCreateWithArguments() {       
        
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
        $this->assertObjectPropertyEquals($object, 'function', $this->piFunction);   
        $this->assertObjectPropertyEquals($object, 'argumentCommands', array($argumentCommand));
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Exception\UnknownFunctionException
     */
    public function testCreateWithNotExistingFunction() {       
        
        $options = array(
            'name' => 'notExistingFunction',
        );
        $this->factory->create($options);
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Command\CommandFactory\CommandFactoryException
     */
    public function testCreateWithMissingNameOption() {
        $this->factory->create(array());
    }
        
    protected function assertObjectPropertyEquals($object, $property, $expected) {
        $this->assertEquals(PHPUnit_Framework_Assert::readAttribute($object, $property), $expected);
    }
    
}

