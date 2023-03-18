<?php

use Mormat\FormulaInterpreter\Parser\StringParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

/**
 * Tests the parsing of strings
 *
 * @author mormat
 */
class StringParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $this->parser = new StringParser();
    }
    
    /**
     * @dataProvider getStringValues
     */
    public function testParseString($expression, $infos) {
        $infos['type'] = 'string';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getStringValues() {
        return array(
            array("'foo'",     array('value' => 'foo')),
            array("'bar'",     array('value' => 'bar')),
        );
    }
    
    /**
     * @expectedException Mormat\FormulaInterpreter\Parser\ParserException
     * @dataProvider getUncorrectExpressionData
     */
    public function testParseUncorrectExpression($expression) {
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressionData() {
        return array(
            array("2"),
            array("foo"),
            array("'foo'bar'"),
            array("''foobar'"),
        );
    }
    
}
