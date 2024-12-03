<?php

use \Mormat\FormulaInterpreter\Compiler;
use \Mormat\FormulaInterpreter\Functions\CallableFunction;
use \Mormat\FormulaInterpreter\Exception\UnknownFunctionException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompilerTest extends TestCase {
    
    #[DataProvider('getCompileAndRunData')]
    public function testCompileAndRun($expression, $result, $variables = []) {
        $compiler = new Compiler();
        $compiler->registerCustomFunction(
            new CallableFunction('get_integer_part', 'floor', ['numeric']),
        );
        $compiler->registerCustomFunction(
            new CallableFunction(
                'equal', 
                fn($a,$b) => intval($a == $b), 
                ['numeric', 'numeric']
            )
        );
        
        $executable = $compiler->compile($expression);
        $this->assertEquals($result, $executable->run($variables));

    }
    
    public static function getCompileAndRunData() {
        return array(
            
            array('3', 3),
            array('3 + 3', 6),
            array('price', 10, array('price' => 10)),
            array('price + 2 * 3', 16, array('price' => 10)),
            array('price * 25 / 100', 37.5, new CompilerTest_Variables(['price' => 150])),
            array('pi()', pi()),
            array('pow(3, 2)', 9),
            array('modulo(5, 2)', 1),
            array('cos(0)', 1),
            array('sin(0)', 0),
            array('sqrt(4)', 2),
            array('pow(sqrt(pow(2, 2)), 2)', 4),
            array('get_integer_part(3.4)', 3, []),
            array('modulo(5, two)', 1, ['two' => 2]),
            array('cos(1 * 2) + (3)', 2.5838531634528574),
            
            // Issue #4
            array('(((100 * 0.43075) * 1.1 * 1.5) / (1-0.425)) * 1.105', 136.58520652173917), 
            array('1+(1+1)', 3),
            
            // handling strings
            array("'foobar'", 'foobar'),
            array("lowercase('FOOBAR')", 'foobar'),
            array("uppercase('foobar')", 'FOOBAR'),
            array("capitalize('foobar')", 'Foobar'),
            array("'2 * 3'", '2 * 3'),
            array("count('2 + 2') + 1", 6),
            array("'wars' in lowercase('Star Wars - The Last Hope')", true),
            array("'jedi' in lowercase('Star Wars - The Last Hope')", false),
            // array("count('foobar')", 6, [], ['count' => 'strlen'])
            
            // handling arrays
            array("[1, 2]", [1, 2]),
            array("2 in [0, 1 + 1, 2]", true),
            array("1 in [sin(0)]",      false),
            array("count([0, 1])", 2),
            
            // comparison
            array('1 < 2', true),
            array('1 > 2', false),
            array('1 = 1', true),
            array('1 <= 2', true),
            array('2 <= 1', false),
            
            // issue #16
            ['150+(b*150)*(1+0.05)', 307.5, ['b' => 1]],
            ['(equal(b,1)*150+equal(c,1)) * ( 1-0.05)', 143.45, ['b' => 1, 'c' => 1]],
            ['(a=1)*150', 150, ['a' => 1]],
            ['(a=0)*150', 150, ['a' => 0]],
            
            // allow booleans in *,+,- and / operators #20
            ['(0=1) + 2', 2],
            ['2 + (1=1)', 3],
            ['(0=1) - 2', -2],
            ['2 - (1=1)', 1],
            ['(0=1) * 2', 0],
            ['2 * (1=1)', 2],
            ['(0=1) / 2', 0],
            ['2 / (1=1)', 2]
        );
    }
    
    #[DataProvider('getCompileInvalidExpressionData')]
    public function testCompileInvalidExpressions($expression, $expectedException)
    {
        $this->expectException($expectedException);
        
        $compiler = new Compiler();
        $executable = $compiler->compile($expression);
        $executable->run();
    }
    
    static function getCompileInvalidExpressionData() 
    {
        return array(
            array('get_integer_part(2)', UnknownFunctionException::class),
        );
    }
        
    
    public function testGetAvailableOperators()
        {
        $compiler = new Compiler();
        
        $actual = $compiler->getAvailableOperators();
        
        $this->assertEquals(
            $actual,
            array(
                '+'  => [
                    'name' => 'add',
                    'supportedTypes' => ['bool|numeric', 'bool|numeric'],
                ],
                '-'  => [
                    'name' => 'subtract',
                    'supportedTypes' => ['bool|numeric', 'bool|numeric'],
                ],
                '*'  => [
                    'name' => 'multiply',
                    'supportedTypes' => ['bool|numeric', 'bool|numeric'],
                ],
                '/'  => [
                    'name' => 'divide',
                    'supportedTypes' => ['bool|numeric', 'bool|numeric'],
                ],
                '<'  => [
                    'name' => 'lower',
                    'supportedTypes' => ['numeric|string', 'numeric|string'],
                ],
                '>'  => [
                    'name' => 'greater',
                    'supportedTypes' => ['numeric|string', 'numeric|string'],
                ],
                '='  => [
                    'name' => 'equal',
                    'supportedTypes' => ['numeric|string', 'numeric|string'],
                ],
                "<=" => [
                    'name' => 'lower_or_equal',
                    'supportedTypes' => ['numeric|string', 'numeric|string'],
                ],
                ">=" => [
                    'name' => 'greater_or_equal',
                    'supportedTypes' => ['numeric|string', 'numeric|string'],
                ],
                "in" => [
                    'name' => 'in',
                    'supportedTypes' => ['numeric|string', 'array|string'],
                ]   
            ),
            sprintf('actual values : %s', json_encode($actual, JSON_PRETTY_PRINT))
        );
    }
    
    public function testGetRegisteredFunctions()
    {
        $compiler = new Compiler();
        
        $actual = $compiler->getRegisteredFunctions();
                
        $this->assertEquals(
            $actual,
            array(
                'pi'    => [
                    'name' => 'pi', 
                    'supportedTypes' => []
                ],
                'cos'   => [
                    'name' => 'cos', 
                    'supportedTypes' => ['numeric']
                ],
                'sin'   => [
                    'name' => 'sin', 
                    'supportedTypes' => ['numeric']
                ],
                'sqrt'  => [
                    'name' => 'sqrt', 
                    'supportedTypes' => ['numeric']
                ],
                'pow'   => [
                    'name' => 'pow', 
                    'supportedTypes' => ['numeric', 'numeric']
                ],
                'modulo'     => [
                    'name' => 'modulo', 
                    'supportedTypes' => ['numeric', 'numeric']
                ],
                'lowercase'  => [
                    'name' => 'lowercase', 
                    'supportedTypes' => ['string']
                ],
                'uppercase'  => [
                    'name' => 'uppercase', 
                    'supportedTypes' => ['string']
                ],
                'capitalize' => [
                    'name' => 'capitalize', 
                    'supportedTypes' => ['string']
                ],
                'count'      => [
                    'name' => 'count', 
                    'supportedTypes' => ['string|array']
                ],
            ),
            sprintf('actual values : %s', json_encode($actual, JSON_PRETTY_PRINT))
        );
        
    }
    
}

/**
 * @todo replace with getMockBuilder
 */
class CompilerTest_Variables implements \ArrayAccess {
    
    protected $variables = array();
    
    public function __construct(array $variables) {
        $this->variables = $variables;
    }

    
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->variables);
    }

    public function offsetGet($offset) {
        if (array_key_exists($offset, $this->variables)) {
            return $this->variables[$offset];
        }
    }

    public function offsetSet($offset, $value) {
        throw new \Exception("not implemented");
    }

    public function offsetUnset($offset) {
        throw new \Exception("not implemented");
    }

}