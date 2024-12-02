<?php

use Mormat\FormulaInterpreter\Parser\VariableParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parsing of variables
 *
 * @author mormat
 */
class VariableParserTest extends TestCase {
    
    protected VariableParser $parser;
    
    public function setUp(): void {
        $this->parser = new VariableParser();
    }

    #[DataProvider('getCorrectExpressions')]
    public function testParseCorrectExpression($expression, $infos) {
        $infos['type'] = 'variable';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public static function getCorrectExpressions() {
        return array(
            array('price', array('name' => 'price')),
            array('rate', array('name' => 'rate')),
            array(' rate ', array('name' => 'rate')),
            array('with_underscore', array('name' => 'with_underscore')),
            array('camelCase', array('name' => 'camelCase')),
            array('rate2', array('name' => 'rate2')),
        );
    }
    
    #[DataProvider('getUncorrectExpressionData')]
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        
        $this->parser->parse($expression);
    }
    
    public static function getUncorrectExpressionData() {
        return array(
            array(''),
            array('23'),
            array(' 23 '),
            array('23 12'),
            array(' some_function( '),
        );
    }
    
}
