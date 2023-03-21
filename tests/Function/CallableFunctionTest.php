<?php

use Mormat\FormulaInterpreter\Functions\CallableFunction;

/**
 * Tests functions with single parameter
 *
 * @author mormat
 */
class CallableFunctionTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getTestSupportsData
     */
    public function testSupports2($callable, $params, $supportedTypes = [])
    {
        $function = new CallableFunction($callable, $supportedTypes);
        $this->assertTrue($function->supports($params));
    }
    
    public function getTestSupportsData() {
        return array(
            array('floor',  [2],        ['numeric']),
            array('floor',  [2.3],      ['numeric']),
            array('floor',  ['2.3'],    ['numeric']),
            array('floor',  ['2'],      ['numeric']),
            array('sizeof', [array(2)], ['array']),
        );
    }
    
    /**
     * @dataProvider getSupportsWithInvalidParametersData
     */
    public function testSupportsWithInvalidParameters($callable, $params, $supportedTypes)
    {
        $function = new CallableFunction($callable, $callable, $supportedTypes);
        $this->assertFalse($function->supports($params));
    }
    
    public function getSupportsWithInvalidParametersData()
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
    
    /**
     * @dataProvider getExecuteData
     */
    public function testExecute($callable, $params, $expectedResult)
    {
        $function = new CallableFunction($callable, $callable, $params);
        $this->assertEquals($function->execute($params), $expectedResult);
    }
    
    public function getExecuteData() 
    {
        return array(
            array('floor', [2.3], 2)
        );
    }
    
}
