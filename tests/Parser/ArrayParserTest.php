<?php

use Mormat\FormulaInterpreter\Parser\ArrayParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

/**
 * Tests the parsing of arrays
 *
 * @author mormat
 */
class ArrayParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
        $itemParser = $this->getMockBuilder(
            ParserInterface::class
        )->getMock();
        $itemParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockItemParser')));
        $this->parser = new ArrayParser($itemParser);
        
    }
    
    /**
     * @dataProvider getParseIfExpressionisCorrectData
     */
    public function testParseIfExpressionisCorrect($expression, $expected) {
        $expected['type'] = 'array';
        $this->assertEquals($this->parser->parse($expression), $expected);
    }
    
    public function getParseIfExpressionisCorrectData() {
        return array(
            array("[]",  array('value' => [])),
            array("[1]", array('value' => ['item 1'])),
            array("[2,3]", array('value' => ['item 2', "item 3"])),
        );
    }
    
    /**
     * @dataProvider getParseIfExpressionisUncorrectData
     */
    public function testParseIfExpressionisUncorrect($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        $this->parser->parse($expression);
    }
    
    public function getParseIfExpressionisUncorrectData() {
        return array(
            array(""),
            array(" "),
            array(" foo "),
        );
    }
    
    /**
     * @dataProvider getSplitExtensionData
     */
    public function testSplitExpression($expression, $expected) {
        $actual = $this->parser->explode($expression, [',']);
        $this->assertEquals(
            $actual, 
            $expected,
            sprintf("actual value is %s", var_export($actual, true))
        );
    }
    
    public function getSplitExtensionData() {
        return array(
            
            array(
                " foo ",
                [" foo "]
            ),
            
            array(
                "foo, bar",
                ["foo", ",", " bar"]
            ),
            
            array(
                " [foo, bar] ",
                [" [foo, bar] "]
            ),
            
            array(
                " func(2, 4) ",
                [" func(2, 4) "]
            ),
            
            array(
                " '2, 4' ",
                [" '2, 4' "]
            ),
            
        );
    }
    
    public function mockItemParser($expression)
    {
        return 'item ' . $expression;
    }
    
}
