<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Functions\CallableFunction;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CallableFunctionTest extends TestCase
{
    #[DataProvider('getTestSupportsData')]
    public function testSupports2($callable, $params, $supportedTypes = [])
    {
        $function = new CallableFunction($callable, $supportedTypes);
        $this->assertTrue($function->supports($params));
    }
    
    public static function getTestSupportsData()
    {
        return array(
            array('floor',  [2],        ['numeric']),
            array('floor',  [2.3],      ['numeric']),
            array('floor',  ['2.3'],    ['numeric']),
            array('floor',  ['2'],      ['numeric']),
            array('sizeof', [array(2)], ['array']),
        );
    }
    
    #[DataProvider('getSupportsWithInvalidParametersData')]
    public function testSupportsWithInvalidParameters(
        $callable,
        $params,
        $supportedTypes
    ) {
        $function = new CallableFunction($callable, $callable, $supportedTypes);
        $this->assertFalse($function->supports($params));
    }
    
    public static function getSupportsWithInvalidParametersData()
    {
        return array(
            
            // at least, one parameter required
            array('floor', [],        ['numeric']),
            
            // strings not allowed
            array('floor', ['foobar'], ['numeric']),
            
            // objects not allowed
            array('floor', [ new \stdClass() ], ['numeric']),
            
            // 2 arguments required
            array('modulo', [1],   ['numeric', 'numeric']),
            
            // string only required
            array('strtolower', [1], ['string']),
            
            // array only
            array('sizeof', [2], ['array']),
        );
    }
    
    #[DataProvider('getExecuteData')]
    public function testExecute($callable, $params, $expectedResult)
    {
        $function = new CallableFunction($callable, $callable, $params);
        $this->assertEquals($function->execute($params), $expectedResult);
    }
    
    public static function getExecuteData()
    {
        return array(
            array('floor', [2.3], 2)
        );
    }
}
