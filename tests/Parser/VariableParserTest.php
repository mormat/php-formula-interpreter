<?php

use Mormat\FormulaInterpreter\Parser\VariableParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

/**
 * Tests the parsing of variables
 *
 * @author mormat
 */
class VariableParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $this->parser = new VariableParser();
    }

    /**
     * @dataProvider getCorrectExpressions
     */
    public function testParseCorrectExpression($expression, $infos) {
        $infos['type'] = 'variable';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getCorrectExpressions() {
        return array(
            array('price', array('name' => 'price')),
            array('rate', array('name' => 'rate')),
            array(' rate ', array('name' => 'rate')),
            array('with_underscore', array('name' => 'with_underscore')),
            array('camelCase', array('name' => 'camelCase')),
            array('rate2', array('name' => 'rate2')),
        );
    }
    
    /**
     * @dataProvider getUncorrectExpressionData
     */
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressionData() {
        return array(
            array(''),
            array('23'),
            array(' 23 '),
            array('23 12'),
            array(' some_function( '),
        );
    }
    
}
